<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Cookieconsent\ViewProviders;

use ACP3\Core\Helpers\Forms;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Cookieconsent\Installer\Schema;

class AdminSettingsViewProvider
{
    public function __construct(private Forms $formsHelper, private FormToken $formTokenHelper, private RequestInterface $request, private SettingsInterface $settings)
    {
    }

    public function __invoke(): array
    {
        $cookieConsentSettings = $this->settings->getSettings(Schema::MODULE_NAME);

        return [
            'enable' => $this->formsHelper->yesNoCheckboxGenerator(
                'enabled',
                $cookieConsentSettings['enabled']
            ),
            'form' => array_merge($cookieConsentSettings, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }
}
