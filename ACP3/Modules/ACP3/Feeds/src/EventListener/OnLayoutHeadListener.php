<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Feeds\EventListener;

use ACP3\Core\Modules;
use ACP3\Core\View;
use ACP3\Modules\ACP3\Feeds\Installer\Schema;
use ACP3\Modules\ACP3\Feeds\Utility\FeedAvailabilityRegistrar;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnLayoutHeadListener implements EventSubscriberInterface
{
    public function __construct(private readonly View $view, private readonly Modules $modules, private readonly FeedAvailabilityRegistrar $availableFeedsRegistrar)
    {
    }

    public function __invoke(View\Event\TemplateEvent $event): void
    {
        if ($this->modules->isInstalled(Schema::MODULE_NAME)) {
            $this->view->assign('available_feeds', $this->availableFeedsRegistrar->getAvailableModuleNames());

            $event->addContent($this->view->fetchTemplate('Feeds/Partials/head.feed_links.tpl'));
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'core.layout.head' => '__invoke',
        ];
    }
}
