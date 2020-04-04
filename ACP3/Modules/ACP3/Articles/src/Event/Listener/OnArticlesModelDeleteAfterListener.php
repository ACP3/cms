<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Event\Listener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Articles\Cache;

class OnArticlesModelDeleteAfterListener
{
    /**
     * @var Cache
     */
    private $articlesCache;

    public function __construct(Cache $articlesCache)
    {
        $this->articlesCache = $articlesCache;
    }

    public function __invoke(ModelSaveEvent $event)
    {
        if (!$event->isDeleteStatement()) {
            return;
        }

        foreach ($event->getEntryId() as $entryId) {
            $this->articlesCache->getCacheDriver()->delete(Cache::CACHE_ID . $entryId);
        }
    }
}
