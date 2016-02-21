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
class Create extends Core\Modules\AdminController
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
     * @var \ACP3\Modules\ACP3\Newsletter\Helper\SendNewsletter
     */
    protected $newsletterHelpers;

    /**
     * Create constructor.
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
        parent::__construct($context);

        $this->date = $date;
        $this->formTokenHelper = $formTokenHelper;
        $this->newsletterRepository = $newsletterRepository;
        $this->adminFormValidation = $adminFormValidation;
        $this->newsletterHelpers = $newsletterHelpers;
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

        $lang_action = [
            $this->translator->t('newsletter', 'send_and_save'),
            $this->translator->t('newsletter', 'only_save')
        ];

        return [
            'settings' => $settings,
            'test' => $this->get('core.helpers.forms')->yesNoCheckboxGenerator('test', 0),
            'action' => $this->get('core.helpers.forms')->checkboxGenerator('action', [1, 0], $lang_action, 1),
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
                'title' => Core\Functions::strEncode($formData['title']),
                'text' => Core\Functions::strEncode($formData['text'], true),
                'html' => $settings['html'],
                'status' => 0,
                'user_id' => $this->user->getUserId(),
            ];
            $lastId = $this->newsletterRepository->insert($insertValues);

            // Test-Newsletter
            if ($formData['test'] == 1) {
                $bool2 = $this->newsletterHelpers->sendNewsletter($lastId, $settings['mail']);

                $text = $this->translator->t('newsletter', 'create_success');
                $result = $lastId !== false && $bool2 !== false;
            } else {
                $text = $this->translator->t('newsletter', 'save_success');
                $result = $lastId !== false;
            }

            $this->formTokenHelper->unsetFormToken();

            if ($result === false) {
                $text = $this->translator->t('newsletter', 'create_save_error');
            }

            return $this->redirectMessages()->setMessage($result, $text);
        });
    }
}
