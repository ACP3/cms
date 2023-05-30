<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbooknewsletter\EventListener;

use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Modules;
use ACP3\Core\Settings\Event\SettingsSaveEvent;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Guestbooknewsletter\Installer\Schema;
use ACP3\Modules\ACP3\Newsletter\Installer\Schema as NewsletterSchema;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnGuestbookSettingsSaveBeforeEventListener implements EventSubscriberInterface
{
    public function __construct(private readonly Modules $modules, private readonly RequestInterface $request, private readonly SettingsInterface $settings)
    {
    }

    public function __invoke(SettingsSaveEvent $event): void
    {
        if (!$this->modules->isInstalled(NewsletterSchema::MODULE_NAME) || !$this->modules->isInstalled(Schema::MODULE_NAME)) {
            return;
        }

        $this->settings->saveSettings(
            ['newsletter_integration' => (int) $this->request->getPost()->get('newsletter_integration')],
            Schema::MODULE_NAME
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'guestbook.settings.save_before' => '__invoke',
        ];
    }
}
