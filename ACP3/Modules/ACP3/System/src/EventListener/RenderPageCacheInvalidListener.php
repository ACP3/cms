<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\EventListener;

use ACP3\Core\ACL;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View;
use ACP3\Core\View\Event\TemplateEvent;
use ACP3\Modules\ACP3\System\Installer\Schema;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RenderPageCacheInvalidListener implements EventSubscriberInterface
{
    public function __construct(private readonly ACL $acl, private readonly SettingsInterface $settings, private readonly View $view)
    {
    }

    public function __invoke(TemplateEvent $event): void
    {
        $systemSettings = $this->settings->getSettings(Schema::MODULE_NAME);
        if ($this->acl->hasPermission('admin/system/maintenance/cache') && $systemSettings['page_cache_is_valid'] == 0) {
            $event->addContent($this->view->fetchTemplate('System/Partials/alert_invalid_page_cache.tpl'));
        }
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
