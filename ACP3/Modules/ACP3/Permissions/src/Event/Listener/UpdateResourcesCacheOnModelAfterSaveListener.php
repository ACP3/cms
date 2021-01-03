<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Event\Listener;

use ACP3\Modules\ACP3\Permissions\Cache;

class UpdateResourcesCacheOnModelAfterSaveListener
{
    /**
     * @var Cache
     */
    private $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function __invoke()
    {
        $this->cache->saveResourcesCache();
    }
}
