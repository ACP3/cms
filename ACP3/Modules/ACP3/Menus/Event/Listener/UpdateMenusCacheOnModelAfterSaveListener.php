<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Menus\Event\Listener;

use ACP3\Modules\ACP3\Menus\Cache\MenusCacheStorage;

class UpdateMenusCacheOnModelAfterSaveListener
{
    /**
     * @var MenusCacheStorage
     */
    protected $cache;

    /**
     * UpdateMenusCacheOnModelAfterSaveListener constructor.
     * @param MenusCacheStorage $cache
     */
    public function __construct(MenusCacheStorage $cache)
    {
        $this->cache = $cache;
    }

    public function execute()
    {
        $this->cache->saveMenusCache();
    }
}
