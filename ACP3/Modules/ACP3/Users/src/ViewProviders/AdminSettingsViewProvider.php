<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\ViewProviders;

use ACP3\Core\Helpers\Forms;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Users\Installer\Schema as UsersSchema;

class AdminSettingsViewProvider
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    private $formTokenHelper;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    private $formsHelpers;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;

    public function __construct(
        FormToken $formTokenHelper,
        Forms $formsHelpers,
        RequestInterface $request,
        SettingsInterface $settings
    ) {
        $this->formTokenHelper = $formTokenHelper;
        $this->formsHelpers = $formsHelpers;
        $this->request = $request;
        $this->settings = $settings;
    }

    public function __invoke(): array
    {
        $settings = $this->settings->getSettings(UsersSchema::MODULE_NAME);

        return [
            'registration' => $this->formsHelpers->yesNoCheckboxGenerator(
                'enable_registration',
                $settings['enable_registration']
            ),
            'form' => array_merge(['mail' => $settings['mail']], $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }
}
