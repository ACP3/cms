<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Cookieconsent\EventListener;

use ACP3\Core\Assets\Libraries;
use ACP3\Core\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Cookieconsent\Installer\Schema;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EnableConsentManagementAssetsListener implements EventSubscriberInterface
{
    public function __construct(private Modules $modules, private Libraries $libraries, private SettingsInterface $settings)
    {
    }

    public function __invoke(): void
    {
        if (!$this->modules->isInstalled(Schema::MODULE_NAME)) {
            return;
        }

        $cookieConsentSettings = $this->settings->getSettings(Schema::MODULE_NAME);

        if (false === (bool) $cookieConsentSettings['enabled']) {
            return;
        }

        $this->libraries->enableLibraries(['consentManager']);
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'layout.content_before' => '__invoke',
        ];
    }
}
