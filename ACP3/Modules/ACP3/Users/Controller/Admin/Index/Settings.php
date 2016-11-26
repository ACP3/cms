<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Users;

/**
 * Class Settings
 * @package ACP3\Modules\ACP3\Users\Controller\Admin\Index
 */
class Settings extends Core\Controller\AbstractAdminAction
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
     * @param \ACP3\Core\Controller\Context\AdminContext                      $context
     * @param \ACP3\Core\Helpers\FormToken                                    $formTokenHelper
     * @param \ACP3\Core\Helpers\Forms                                        $formsHelpers
     * @param \ACP3\Modules\ACP3\Users\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
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
     * @return array
     */
    public function execute()
    {
        $settings = $this->config->getSettings(Users\Installer\Schema::MODULE_NAME);

        return [
            'languages' => $this->formsHelpers->yesNoCheckboxGenerator(
                'language_override',
                $settings['language_override']
            ),
            'registration' => $this->formsHelpers->yesNoCheckboxGenerator(
                'enable_registration',
                $settings['enable_registration']
            ),
            'form' => array_merge(['mail' => $settings['mail']], $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken()
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost()
    {
        return $this->actionHelper->handleSettingsPostAction(function () {
            $formData = $this->request->getPost()->all();

            $this->adminSettingsFormValidation->validate($formData);

            $data = [
                'enable_registration' => $formData['enable_registration'],
                'language_override' => $formData['language_override'],
                'mail' => $formData['mail']
            ];

            return $this->config->saveSettings($data, Users\Installer\Schema::MODULE_NAME);
        });
    }
}
