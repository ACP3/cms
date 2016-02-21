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
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Model\NewsletterRepository
     */
    protected $newsletterRepository;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Validation\AdminFormValidation
     */
    protected $adminFormValidation;

    /**
     * Edit constructor.
     *
     * @param \ACP3\Core\Modules\Controller\AdminContext                   $context
     * @param \ACP3\Core\Date                                              $date
     * @param \ACP3\Core\Helpers\FormToken                                 $formTokenHelper
     * @param \ACP3\Modules\ACP3\Newsletter\Model\NewsletterRepository     $newsletterRepository
     * @param \ACP3\Modules\ACP3\Newsletter\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Modules\ACP3\Newsletter\Helper\SendNewsletter          $newsletterHelpers
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Date $date,
        Core\Helpers\FormToken $formTokenHelper,
        Newsletter\Model\NewsletterRepository $newsletterRepository,
        Newsletter\Validation\AdminFormValidation $adminFormValidation,
        Newsletter\Helper\SendNewsletter $newsletterHelpers)
    {
        parent::__construct($context, $newsletterHelpers);

        $this->date = $date;
        $this->formTokenHelper = $formTokenHelper;
        $this->newsletterRepository = $newsletterRepository;
        $this->adminFormValidation = $adminFormValidation;
    }

    /**
     * @param int $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function execute($id)
    {
        $newsletter = $this->newsletterRepository->getOneById($id);

        if (empty($newsletter) === false) {
            $this->breadcrumb->setTitlePostfix($newsletter['title']);

            $settings = $this->config->getSettings('newsletter');

            if ($this->request->getPost()->isEmpty() === false) {
                return $this->executePost($this->request->getPost()->all(), $settings, $id);
            }

            $lang_action = [
                $this->translator->t('newsletter', 'send_and_save'),
                $this->translator->t('newsletter', 'only_save')
            ];

            return [
                'settings' => array_merge($settings, ['html' => $newsletter['html']]),
                'test' => $this->get('core.helpers.forms')->yesNoCheckboxGenerator('test', 0),
                'action' => $this->get('core.helpers.forms')->checkboxGenerator('action', [1, 0], $lang_action, 1),
                'form' => array_merge($newsletter, $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken()
            ];
        }

        throw new Core\Exceptions\ResultNotExists();
    }

    /**
     * @param array $formData
     * @param array $settings
     * @param int   $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, array $settings, $id)
    {
        return $this->actionHelper->handlePostAction(function () use ($formData, $settings, $id) {
            $this->adminFormValidation->validate($formData);

            // Newsletter archivieren
            $updateValues = [
                'date' => $this->date->toSQL($formData['date']),
                'title' => Core\Functions::strEncode($formData['title']),
                'text' => Core\Functions::strEncode($formData['text'], true),
                'user_id' => $this->user->getUserId(),
            ];
            $bool = $this->newsletterRepository->update($updateValues, $id);

            list($text, $result) = $this->sendTestNewsletter(
                $formData['test'] == 1,
                $id,
                $bool,
                $settings['mail']
            );

            $this->formTokenHelper->unsetFormToken();

            return $this->redirectMessages()->setMessage($result, $text);
        });
    }
}
