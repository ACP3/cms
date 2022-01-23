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
    public function __construct(private FormToken $formTokenHelper, private Forms $formsHelpers, private RequestInterface $request, private SettingsInterface $settings)
    {
    }

    /**
     * @return array<string, mixed>
     */
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
