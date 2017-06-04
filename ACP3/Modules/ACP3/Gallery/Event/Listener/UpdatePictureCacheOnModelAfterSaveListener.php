<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Event\Listener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Gallery\Cache\GalleryCacheStorage;

class UpdatePictureCacheOnModelAfterSaveListener
{
    /**
     * @var GalleryCacheStorage
     */
    protected $cache;

    /**
     * UpdatePictureCacheOnModelAfterSaveListener constructor.
     * @param GalleryCacheStorage $cache
     */
    public function __construct(GalleryCacheStorage $cache)
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
