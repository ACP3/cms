<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Event\Listener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Articles\Cache\ArticleCacheStorage;

class UpdateArticleCacheOnModelSaveAfterListener
{
    /**
     * @var ArticleCacheStorage
     */
    protected $cache;

    /**
     * UpdateArticleCacheOnModelSaveAfterListener constructor.
     *
     * @param ArticleCacheStorage $cache
     */
    public function __construct(ArticleCacheStorage $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param ModelSaveEvent $event
     */
    public function execute(ModelSaveEvent $event)
    {
        $this->cache->saveCache($event->getEntryId());
    }
}
