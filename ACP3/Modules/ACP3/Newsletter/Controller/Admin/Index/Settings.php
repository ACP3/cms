<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;

/**
 * Class Settings
 * @package ACP3\Modules\ACP3\Newsletter\Controller\Admin\Index
 */
class Settings extends Core\Controller\AdminController
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Validation\AdminSettingsFormValidation
     */
    protected $adminSettingsFormValidation;

    /**
     * Settings constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext                           $context
     * @param \ACP3\Core\Helpers\FormToken                                         $formTokenHelper
     * @param \ACP3\Modules\ACP3\Newsletter\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Newsletter\Validation\AdminSettingsFormValidation $adminSettingsFormValidation)
    {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->adminSettingsFormValidation = $adminSettingsFormValidation;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            return $this->executePost($this->request->getPost()->all());
        }

        $settings = $this->config->getSettings('newsletter');

        return [
            'html' => $this->get('core.helpers.forms')->yesNoCheckboxGenerator('html', $settings['html']),
            'form' => array_merge($settings, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken()
        ];
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData)
    {
        return $this->actionHelper->handleSettingsPostAction(function () use ($formData) {
            $this->adminSettingsFormValidation->validate($formData);

            $data = [
                'mail' => $formData['mail'],
                'mailsig' => Core\Functions::strEncode($formData['mailsig'], true),
                'html' => (int)$formData['html']
            ];

            $this->formTokenHelper->unsetFormToken();

            return $this->config->setSettings($data, 'newsletter');
        });
    }
}
