<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Cookieconsent\Event\Listener;

use ACP3\Core\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View;
use ACP3\Modules\ACP3\Cookieconsent\Installer\Schema;

class RenderCookieConsentListener
{
    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var View
     */
    private $view;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;

    public function __construct(Modules $modules, SettingsInterface $settings, View $view)
    {
        $this->settings = $settings;
        $this->view = $view;
        $this->modules = $modules;
    }

    public function __invoke()
    {
        if (!$this->modules->isActive(Schema::MODULE_NAME)) {
            return;
        }

        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        if ((int) $settings['enabled'] === 1 && !empty($settings['text'])) {
            $this->view->assign('cookie_consent_text', $settings['text']);
            $this->view->displayTemplate('Cookieconsent/Partials/cookie_consent.tpl');
        }
    }
}
