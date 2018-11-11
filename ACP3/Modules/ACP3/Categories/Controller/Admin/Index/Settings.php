<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;

class Settings extends Core\Controller\AbstractFormAction
{
    /**
     * @var \ACP3\Modules\ACP3\Categories\Validation\AdminSettingsFormValidation
     */
    protected $adminSettingsFormValidation;
    /**
     * @var Core\Helpers\FormToken
     */
    protected $formTokenHelper;

    public function __construct(
        Core\Controller\Context\FormContext $context,
        Categories\Validation\AdminSettingsFormValidation $adminSettingsFormValidation,
        Core\Helpers\FormToken $formTokenHelper
    ) {
        parent::__construct($context);

        $this->adminSettingsFormValidation = $adminSettingsFormValidation;
        $this->formTokenHelper = $formTokenHelper;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $settings = $this->config->getSettings(Categories\Installer\Schema::MODULE_NAME);

        return [
            'form' => \array_merge($settings, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function executePost()
    {
        return $this->actionHelper->handleSettingsPostAction(function () {
            $formData = $this->request->getPost()->all();
            $this->adminSettingsFormValidation->validate($formData);

            $data = [
                'width' => (int) $formData['width'],
                'height' => (int) $formData['height'],
                'filesize' => (int) $formData['filesize'],
            ];

            return $this->config->saveSettings($data, Categories\Installer\Schema::MODULE_NAME);
        });
    }
}
