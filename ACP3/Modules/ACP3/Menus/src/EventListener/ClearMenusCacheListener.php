<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\EventListener;

use ACP3\Core\Cache;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ClearMenusCacheListener implements EventSubscriberInterface
{
    /**
     * @var Cache
     */
    private $menusCache;

    public function __construct(Cache $menusCache)
    {
        $this->menusCache = $menusCache;
    }

    public function __invoke()
    {
        $this->menusCache->deleteAll();
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
