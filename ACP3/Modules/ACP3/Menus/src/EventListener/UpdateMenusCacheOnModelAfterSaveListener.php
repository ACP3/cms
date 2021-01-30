<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\EventListener;

use ACP3\Modules\ACP3\Menus\Cache;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UpdateMenusCacheOnModelAfterSaveListener implements EventSubscriberInterface
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
        $this->cache->saveMenusCache();
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'menus.model.menus.after_save' => '__invoke',
            'menus.model.menu_items.after_save' => '__invoke',
            'menus.model.menu_items.after_delete' => '__invoke',
        ];
    }
}
