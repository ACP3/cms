<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;

/**
 * Class Edit
 * @package ACP3\Modules\ACP3\Newsletter\Controller\Admin\Index
 */
class Edit extends AbstractFormAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;
    /**
     * @var Newsletter\Model\NewsletterModel
     */
    protected $newsletterModel;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param \ACP3\Core\Helpers\Forms $formsHelper
     * @param \ACP3\Core\Helpers\FormToken $formTokenHelper
     * @param Newsletter\Model\NewsletterModel $newsletterModel
     * @param \ACP3\Modules\ACP3\Newsletter\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Modules\ACP3\Newsletter\Helper\SendNewsletter $newsletterHelpers
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Newsletter\Model\NewsletterModel $newsletterModel,
        Newsletter\Validation\AdminFormValidation $adminFormValidation,
        Newsletter\Helper\SendNewsletter $newsletterHelpers)
    {
        parent::__construct($context, $newsletterHelpers);

        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->newsletterModel = $newsletterModel;
        $this->adminFormValidation = $adminFormValidation;
    }

    /**
     * @param int $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        $newsletter = $this->newsletterModel->getOneById($id);

        if (empty($newsletter) === false) {
            $this->title->setPageTitlePostfix($newsletter['title']);

            $settings = $this->config->getSettings(Newsletter\Installer\Schema::MODULE_NAME);

            if ($this->request->getPost()->count() !== 0) {
                return $this->executePost($this->request->getPost()->all(), $newsletter, $settings, $id);
            }

            $actions = [
                1 => $this->translator->t('newsletter', 'send_and_save'),
                0 => $this->translator->t('newsletter', 'only_save')
            ];

            return [
                'settings' => array_merge($settings, ['html' => $newsletter['html']]),
                'test' => $this->formsHelper->yesNoCheckboxGenerator('test', 0),
                'action' => $this->formsHelper->checkboxGenerator('action', $actions, 1),
                'form' => array_merge($newsletter, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken()
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param array $formData
     * @param array $newsletter
     * @param array $settings
     * @param int   $newsletterId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, array $newsletter, array $settings, $newsletterId)
    {
        return $this->actionHelper->handlePostAction(function () use ($formData, $newsletter, $settings, $newsletterId) {
            $this->adminFormValidation->validate($formData);

            $bool = $this->newsletterModel->saveNewsletter($formData, $this->user->getUserId(), $newsletterId);

            list($text, $result) = $this->sendTestNewsletter(
                $formData['test'] == 1,
                $newsletterId,
                $bool,
                $settings['mail']
            );

            return $this->redirectMessages()->setMessage($result, $text);
        });
    }
}
