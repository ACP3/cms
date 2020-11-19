<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Cookieconsent\Event\Listener;

use ACP3\Core\Assets\Event\AddLibraryEvent;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Cookieconsent\Installer\Schema;

class AddConsentManagementAssetsListener
{
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;

    public function __construct(SettingsInterface $settings)
    {
        $this->settings = $settings;
    }

    public function __invoke(AddLibraryEvent $event): void
    {
        $cookieConsentSettings = $this->settings->getSettings(Schema::MODULE_NAME);

        if (false === (bool) $cookieConsentSettings['enabled']) {
            return;
        }

        $event->addLibrary('consentManager', [
            'enabled' => true,
            'enabled_for_ajax' => false,
            'dependencies' => [],
            'module' => 'cookieconsent',
            'css' => 'klaro.css',
            'js' => ['klaro-config.js', 'klaro-no-css.js'],
        ]);
    }
}
