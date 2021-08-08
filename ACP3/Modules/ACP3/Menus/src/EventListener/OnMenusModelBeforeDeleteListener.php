<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\EventListener;

use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Menus\Model\MenuItemsModel;
use ACP3\Modules\ACP3\Menus\Repository\MenuItemRepository;
use ACP3\Modules\ACP3\Menus\Repository\MenuRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnMenusModelBeforeDeleteListener implements EventSubscriberInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Menus\Repository\MenuRepository
     */
    private $menuRepository;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Repository\MenuItemRepository
     */
    private $menuItemRepository;
    /**
     * @var MenuItemsModel
     */
    private $menuItemsModel;

    public function __construct(
        MenuRepository $menuRepository,
        MenuItemRepository $menuItemRepository,
        MenuItemsModel $menuItemsModel
    ) {
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
