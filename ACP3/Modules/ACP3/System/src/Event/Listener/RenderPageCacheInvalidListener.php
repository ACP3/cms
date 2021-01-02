<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Event\Listener;

use ACP3\Core\ACL;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View;
use ACP3\Core\View\Event\TemplateEvent;
use ACP3\Modules\ACP3\System\Installer\Schema;

class RenderPageCacheInvalidListener
{
    /**
     * @var ACL
     */
    private $acl;
    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var View
     */
    private $view;

    public function __construct(ACL $acl, SettingsInterface $settings, View $view)
    {
        $this->acl = $acl;
        $this->settings = $settings;
        $this->view = $view;
    }

    public function __invoke(TemplateEvent $event): void
    {
        $systemSettings = $this->settings->getSettings(Schema::MODULE_NAME);
        if ($this->acl->hasPermission('admin/system/maintenance/cache') && $systemSettings['page_cache_is_valid'] == 0) {
            $event->addContent($this->view->fetchTemplate('System/Partials/alert_invalid_page_cache.tpl'));
        }
    }
}
