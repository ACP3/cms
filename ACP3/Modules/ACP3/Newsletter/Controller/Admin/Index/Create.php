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
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;

    /**
     * Create constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext                   $context
     * @param \ACP3\Core\Date                                              $date
     * @param \ACP3\Core\Helpers\Forms                                     $formsHelper
     * @param \ACP3\Core\Helpers\FormToken                                 $formTokenHelper
     * @param \ACP3\Modules\ACP3\Newsletter\Model\NewsletterRepository     $newsletterRepository
     * @param \ACP3\Modules\ACP3\Newsletter\Validation\AdminFormValidation $adminFormValidation
     * @param \ACP3\Modules\ACP3\Newsletter\Helper\SendNewsletter          $newsletterHelpers
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Date $date,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Newsletter\Model\NewsletterRepository $newsletterRepository,
        Newsletter\Validation\AdminFormValidation $adminFormValidation,
        Newsletter\Helper\SendNewsletter $newsletterHelpers)
    {
        parent::__construct($context, $newsletterHelpers);

        $this->date = $date;
        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->newsletterRepository = $newsletterRepository;
        $this->adminFormValidation = $adminFormValidation;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        $settings = $this->config->getSettings('newsletter');

        if ($this->request->getPost()->isEmpty() === false) {
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

            // Newsletter archivieren
            $insertValues = [
                'id' => '',
                'date' => $this->date->toSQL($formData['date']),
                'title' => $this->get('core.helpers.secure')->strEncode($formData['title']),
                'text' => $this->get('core.helpers.secure')->strEncode($formData['text'], true),
                'html' => $settings['html'],
                'status' => 0,
                'user_id' => $this->user->getUserId(),
            ];
            $lastId = $this->newsletterRepository->insert($insertValues);

            list($text, $result) = $this->sendTestNewsletter(
                $formData['test'] == 1,
                $lastId,
                $lastId,
                $settings['mail']
            );

            $this->formTokenHelper->unsetFormToken();

            return $this->redirectMessages()->setMessage($result, $text);
        });
    }
}
