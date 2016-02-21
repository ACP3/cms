<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\Users;

/**
 * Class Settings
 * @package ACP3\Modules\ACP3\Users\Controller\Admin\Index
 */
class Settings extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelpers;
    /**
     * @var \ACP3\Modules\ACP3\Users\Validation\AdminSettingsFormValidation
     */
    protected $adminSettingsFormValidation;

    /**
     * Settings constructor.
     *
     * @param \ACP3\Core\Modules\Controller\AdminContext                      $context
     * @param \ACP3\Core\Helpers\FormToken                                    $formTokenHelper
     * @param \ACP3\Core\Helpers\Forms                                        $formsHelpers
     * @param \ACP3\Modules\ACP3\Users\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Core\Helpers\Forms $formsHelpers,
        Users\Validation\AdminSettingsFormValidation $adminSettingsFormValidation)
    {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->formsHelpers = $formsHelpers;
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

        $settings = $this->config->getSettings('users');

        return [
            'languages' => $this->formsHelpers->yesNoCheckboxGenerator('language_override', $settings['language_override']),
            'entries' => $this->formsHelpers->yesNoCheckboxGenerator('entries_override', $settings['entries_override']),
            'registration' => $this->formsHelpers->yesNoCheckboxGenerator('enable_registration', $settings['enable_registration']),
            'form' => array_merge(['mail' => $settings['mail']], $this->request->getPost()->all()),
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
                'enable_registration' => $formData['enable_registration'],
                'entries_override' => $formData['entries_override'],
                'language_override' => $formData['language_override'],
                'mail' => $formData['mail']
            ];

            $this->formTokenHelper->unsetFormToken();

            return $this->config->setSettings($data, 'users');
        });
    }
}
