<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\Event\Listener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Files\Cache\FileCacheStorage;

class UpdateFileCacheOnModelAfterSaveListener
{
    /**
     * @var FileCacheStorage
     */
    protected $cache;

    /**
     * UpdateFileCacheOnModelAfterSaveListener constructor.
     * @param FileCacheStorage $cache
     */
    public function __construct(FileCacheStorage $cache)
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
