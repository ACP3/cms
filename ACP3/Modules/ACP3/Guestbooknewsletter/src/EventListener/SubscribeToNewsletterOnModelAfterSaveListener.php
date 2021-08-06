<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbooknewsletter\EventListener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Core\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Guestbooknewsletter\Installer\Schema;
use ACP3\Modules\ACP3\Newsletter\Helper\Subscribe;
use ACP3\Modules\ACP3\Newsletter\Installer\Schema as NewsletterSchema;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SubscribeToNewsletterOnModelAfterSaveListener implements EventSubscriberInterface
{
    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Helper\Subscribe
     */
    private $subscribe;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;

    public function __construct(
        Modules $modules,
        SettingsInterface $settings,
        Subscribe $subscribe
    ) {
        $this->settings = $settings;
        $this->subscribe = $subscribe;
        $this->modules = $modules;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(ModelSaveEvent $event)
    {
        if (!$this->modules->isInstalled(NewsletterSchema::MODULE_NAME) || !$this->modules->isInstalled(Schema::MODULE_NAME)) {
            return;
        }

        if (!(bool) $this->settings->getSettings(Schema::MODULE_NAME)['newsletter_integration']) {
            return;
        }

        $formData = $event->getRawData();

        if (!empty($formData['subscribe_newsletter']) && !empty($formData['mail'])) {
            $this->subscribe->subscribeToNewsletter($formData['mail']);
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'guestbook.model.guestbook.after_save' => '__invoke',
        ];
    }
}
