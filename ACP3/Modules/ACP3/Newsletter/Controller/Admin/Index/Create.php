<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;

/**
 * Class Create
 * @package ACP3\Modules\ACP3\Newsletter\Controller\Admin\Index
 */
class Create extends AbstractFormAction
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
     * Create constructor.
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
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        $settings = $this->config->getSettings(Newsletter\Installer\Schema::MODULE_NAME);

        if ($this->request->getPost()->count() !== 0) {
            return $this->executePost($this->request->getPost()->all(), $settings);
        }

        $actions = [
            1 => $this->translator->t('newsletter', 'send_and_save'),
            0 => $this->translator->t('newsletter', 'only_save')
        ];

        return [
            'settings' => $settings,
            'test' => $this->formsHelper->yesNoCheckboxGenerator('test', 0),
            'action' => $this->formsHelper->checkboxGenerator('action', $actions, 1),
            'form' => array_merge(['title' => '', 'text' => '', 'date' => ''], $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken()
        ];
    }

    /**
     * @param array $formData
     * @param array $settings
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, array $settings)
    {
        return $this->actionHelper->handlePostAction(function () use ($formData, $settings) {
            $this->adminFormValidation->validate($formData);

            $newsletterId = $this->newsletterModel->saveNewsletter($formData, $this->user->getUserId());

            list($text, $result) = $this->sendTestNewsletter(
                $formData['test'] == 1,
                $newsletterId,
                $newsletterId,
                $settings['mail']
            );

            return $this->redirectMessages()->setMessage($result, $text);
        });
    }
}
