<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Event\Listener;

use ACP3\Modules\ACP3\Seo\Cache\SeoCacheStorage;

class UpdateSeoCacheOnModelAfterSaveListener
{
    /**
     * @var SeoCacheStorage
     */
    protected $cache;

    /**
     * UpdateSeoCacheOnModelAfterSaveListener constructor.
     * @param SeoCacheStorage $cache
     */
    public function __construct(SeoCacheStorage $cache)
    {
        $this->cache = $cache;
    }

    public function execute()
    {
        $this->cache->saveCache();
    }
}
