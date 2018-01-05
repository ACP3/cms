<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\Event\Listener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\News\Cache\NewsCacheStorage;

class UpdateNewsCacheOnModelAfterSaveListener
{
    /**
     * @var NewsCacheStorage
     */
    protected $cache;

    /**
     * UpdateNewsCacheOnModelAfterSaveListener constructor.
     * @param NewsCacheStorage $cache
     */
    public function __construct(NewsCacheStorage $cache)
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
