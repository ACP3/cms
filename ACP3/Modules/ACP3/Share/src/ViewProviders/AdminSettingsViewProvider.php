<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\ViewProviders;

use ACP3\Core\Helpers\Forms;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Share\Helpers\SocialServices;
use ACP3\Modules\ACP3\Share\Installer\Schema as ShareSchema;

class AdminSettingsViewProvider
{
    public function __construct(private readonly Forms $formsHelper, private readonly FormToken $formTokenHelper, private readonly RequestInterface $request, private readonly SettingsInterface $settings, private readonly SocialServices $socialServices, private readonly Translator $translator)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        $shareSettings = $this->settings->getSettings(ShareSchema::MODULE_NAME);

        return [
            'services' => $this->formsHelper->choicesGenerator(
                'services',
                $this->getServices(),
                unserialize($shareSettings['services'], ['allowed_classes' => false])
            ),
            'form' => array_merge($shareSettings, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function getServices(): array
    {
        $services = [];
        foreach ($this->socialServices->getAllServices() as $service) {
            $services[$service] = $this->translator->t('share', 'service_' . $service);
        }

        return $services;
    }
}
