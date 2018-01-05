<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Event\Listener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Gallery\Cache;

class UpdatePictureCacheOnModelAfterSaveListener
{
    /**
     * @var Cache
     */
    protected $cache;

    /**
     * UpdatePictureCacheOnModelAfterSaveListener constructor.
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param ModelSaveEvent $event
     */
    public function execute(ModelSaveEvent $event)
    {
        $data = $event->getData();

        $this->cache->saveCache($data['gallery_id']);
    }
}
