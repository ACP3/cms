<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\EventListener;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ClearMenusCacheListener implements EventSubscriberInterface
{
    /**
     * @var CacheItemPoolInterface
     */
    private $menusCachePool;

    public function __construct(CacheItemPoolInterface $menusCachePool)
    {
        $this->menusCachePool = $menusCachePool;
    }

    public function __invoke()
    {
        $this->menusCachePool->clear();
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'menus.model.menus.after_save' => '__invoke',
            'menus.model.menus.after_delete' => '__invoke',
            'menus.model.menu_items.after_save' => '__invoke',
            'menus.model.menu_items.after_delete' => '__invoke',
        ];
    }
}
