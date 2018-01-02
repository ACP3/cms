<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Event\Listener;

use ACP3\Modules\ACP3\Permissions\Cache\PermissionsCacheStorage;

class UpdateResourcesCacheOnModelAfterSaveListener
{
    /**
     * @var PermissionsCacheStorage
     */
    protected $cache;

    /**
     * UpdateResourcesCacheOnModelAfterSaveListener constructor.
     * @param PermissionsCacheStorage $cache
     */
    public function __construct(PermissionsCacheStorage $cache)
    {
        $this->cache = $cache;
    }

    public function execute()
    {
        $this->cache->saveResourcesCache();
    }
}
