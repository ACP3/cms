<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Event\Listener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Articles\Cache;
use ACP3\Modules\ACP3\Articles\Helpers;
use ACP3\Modules\ACP3\Menus\Helpers\ManageMenuItem;

class OnArticlesModelDeleteAfterListener
{
    /**
     * @var Cache
     */
    private $articlesCache;
    /**
     * @var ManageMenuItem
     */
    private $manageMenuItemHelper;

    public function __construct(
        Cache $articlesCache,
        ?ManageMenuItem $manageMenuItemHelper
    ) {
        $this->articlesCache = $articlesCache;
        $this->manageMenuItemHelper = $manageMenuItemHelper;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(ModelSaveEvent $event)
    {
        if (!$event->isDeleteStatement()) {
            return;
        }

        foreach ($event->getEntryId() as $entryId) {
            $this->articlesCache->getCacheDriver()->delete(Cache::CACHE_ID . $entryId);

            $uri = \sprintf(Helpers::URL_KEY_PATTERN, $entryId);

            if ($this->manageMenuItemHelper) {
                $this->manageMenuItemHelper->manageMenuItem($uri);
            }
        }
    }
}
