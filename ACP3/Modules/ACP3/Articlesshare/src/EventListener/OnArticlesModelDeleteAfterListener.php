<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articlesshare\EventListener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Core\Modules;
use ACP3\Modules\ACP3\Articles\Helpers;
use ACP3\Modules\ACP3\Share\Helpers\SocialSharingManager;
use ACP3\Modules\ACP3\Share\Installer\Schema as ShareSchema;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnArticlesModelDeleteAfterListener implements EventSubscriberInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Share\Helpers\SocialSharingManager
     */
    private $socialSharingManager;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;

    public function __construct(Modules $modules, SocialSharingManager $socialSharingManager)
    {
        $this->socialSharingManager = $socialSharingManager;
        $this->modules = $modules;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(ModelSaveEvent $event)
    {
        if (!$this->modules->isInstalled(ShareSchema::MODULE_NAME)) {
            return;
        }

        if (!$event->isDeleteStatement()) {
            return;
        }

        foreach ($event->getEntryId() as $entryId) {
            $uri = sprintf(Helpers::URL_KEY_PATTERN, $entryId);

            $this->socialSharingManager->deleteSharingInfo($uri);
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'articles.model.articles.after_delete' => '__invoke',
        ];
    }
}
