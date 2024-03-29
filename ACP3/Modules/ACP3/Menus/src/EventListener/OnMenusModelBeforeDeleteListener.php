<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\EventListener;

use ACP3\Core\Model\Event\BeforeModelDeleteEvent;
use ACP3\Modules\ACP3\Menus\Model\MenuItemsModel;
use ACP3\Modules\ACP3\Menus\Repository\MenuItemRepository;
use ACP3\Modules\ACP3\Menus\Repository\MenuRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnMenusModelBeforeDeleteListener implements EventSubscriberInterface
{
    public function __construct(private readonly MenuRepository $menuRepository, private readonly MenuItemRepository $menuItemRepository, private readonly MenuItemsModel $menuItemsModel)
    {
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(BeforeModelDeleteEvent $event): void
    {
        foreach ($event->getEntryIdList() as $item) {
            if (!empty($item) && $this->menuRepository->menuExists($item) === true) {
                // Delete the assigned menu items and update the nested set tree
                $menuItems = $this->menuItemRepository->getAllItemsByBlockId($item);
                foreach ($menuItems as $menuItem) {
                    $this->menuItemsModel->delete($menuItem['id']);
                }
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'menus.model.menus.before_delete' => '__invoke',
        ];
    }
}
