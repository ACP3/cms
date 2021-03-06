<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\EventListener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Menus\Cache;
use ACP3\Modules\ACP3\Menus\Model\MenuItemsModel;
use ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemRepository;
use ACP3\Modules\ACP3\Menus\Model\Repository\MenuRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnMenusModelBeforeDeleteListener implements EventSubscriberInterface
{
    /**
     * @var Cache
     */
    private $cache;
    /**
     * @var MenuRepository
     */
    private $menuRepository;
    /**
     * @var MenuItemRepository
     */
    private $menuItemRepository;
    /**
     * @var MenuItemsModel
     */
    private $menuItemsModel;

    public function __construct(
        Cache $cache,
        MenuRepository $menuRepository,
        MenuItemRepository $menuItemRepository,
        MenuItemsModel $menuItemsModel
    ) {
        $this->cache = $cache;
        $this->menuRepository = $menuRepository;
        $this->menuItemRepository = $menuItemRepository;
        $this->menuItemsModel = $menuItemsModel;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(ModelSaveEvent $event)
    {
        if (!$event->isDeleteStatement()) {
            return;
        }

        foreach ($event->getEntryId() as $item) {
            if (!empty($item) && $this->menuRepository->menuExists($item) === true) {
                // Delete the assigned menu items and update the nested set tree
                $menuItems = $this->menuItemRepository->getAllItemsByBlockId($item);
                foreach ($menuItems as $menuItem) {
                    $this->menuItemsModel->delete($menuItem['id']);
                }

                $menuName = $this->menuRepository->getMenuNameById($item);
                $this->cache->getCacheDriver()->delete(Cache::CACHE_ID_VISIBLE . $menuName);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'menus.model.menus.before_delete' => '__invoke',
        ];
    }
}
