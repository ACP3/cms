<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbooknewsletter\EventListener;

use ACP3\Core\Helpers\Forms;
use ACP3\Core\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View;
use ACP3\Core\View\Event\TemplateEvent;
use ACP3\Modules\ACP3\Guestbooknewsletter\Installer\Schema;
use ACP3\Modules\ACP3\Newsletter\Installer\Schema as NewsletterSchema;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnGuestbookLayoutSettingsEventListener implements EventSubscriberInterface
{
    public function __construct(private Modules $modules, private View $view, private SettingsInterface $settings, private Forms $formsHelper)
    {
    }

    public function __invoke(TemplateEvent $event): void
    {
        if (!$this->modules->isInstalled(NewsletterSchema::MODULE_NAME) || !$this->modules->isInstalled(Schema::MODULE_NAME)) {
            return;
        }

        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        $this->view->assign(
            'newsletter_integration',
            $this->formsHelper->yesNoCheckboxGenerator('newsletter_integration', $settings['newsletter_integration'])
        );

        $event->addContent($this->view->fetchTemplate('Guestbooknewsletter/Partials/guestbook_layout_settings.tpl'));
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'guestbook.layout.settings' => '__invoke',
        ];
    }
}
