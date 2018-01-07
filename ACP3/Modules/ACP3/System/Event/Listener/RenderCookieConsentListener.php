<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Event\Listener;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View;
use ACP3\Modules\ACP3\System\Installer\Schema;

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
     * OnLayoutFooterAfterListener constructor.
     *
     * @param SettingsInterface $settings
     * @param View              $view
     */
    public function __construct(SettingsInterface $settings, View $view)
    {
        $this->settings = $settings;
        $this->view = $view;
    }

    public function renderCookieConsent()
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        if ($settings['cookie_consent_is_enabled'] == 1 && !empty($settings['cookie_consent_text'])) {
            $this->view->assign('cookie_consent_text', $settings['cookie_consent_text']);
            $this->view->displayTemplate('System/Partials/cookie_consent.tpl');
        }
    }
}
