<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbooknewsletter\Event\Listener;

use ACP3\Core\Helpers\Forms;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View;
use ACP3\Core\View\Event\TemplateEvent;
use ACP3\Modules\ACP3\Guestbooknewsletter\Installer\Schema;
use ACP3\Modules\ACP3\Newsletter\Installer\Schema as NewsletterSchema;
use ACP3\Modules\ACP3\System\Installer\Schema as SystemSchema;

class OnGuestbookLayoutCreateEventListener
{
    /**
     * @var \ACP3\Core\View
     */
    private $view;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    private $formsHelper;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;

    public function __construct(
        Modules $modules,
        SettingsInterface $settings,
        Translator $translator,
        View $view,
        Forms $formsHelper)
    {
        $this->view = $view;
        $this->formsHelper = $formsHelper;
        $this->settings = $settings;
        $this->modules = $modules;
        $this->translator = $translator;
    }

    public function __invoke(TemplateEvent $event): void
    {
        if (!$this->modules->isActive(NewsletterSchema::MODULE_NAME) || !$this->modules->isActive(Schema::MODULE_NAME)) {
            return;
        }

        $guestbookNewsletterSettings = $this->settings->getSettings(Schema::MODULE_NAME);

        if (!(bool) $guestbookNewsletterSettings['newsletter_integration']) {
            return;
        }

        $newsletterSubscription = [
            1 => $this->translator->t(
                'guestbooknewsletter',
                'subscribe_to_newsletter',
                ['%title%' => $this->settings->getSettings(SystemSchema::MODULE_NAME)['site_title']]
            ),
        ];
        $this->view->assign(
            'subscribe_newsletter',
            $this->formsHelper->checkboxGenerator('subscribe_newsletter', $newsletterSubscription, 1)
        );

        $event->addContent($this->view->fetchTemplate('Guestbooknewsletter/Partials/guestbook_layout_create.tpl'));
    }
}
