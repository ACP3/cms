<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Event\Listener;

use ACP3\Core\ACL;
use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Menus\Helpers\ManageMenuItem;

class ManageMenuItemOnModelSaveAfterListener
{
    /**
     * @var ManageMenuItem
     */
    private $menuItemManager;
    /**
     * @var ACL
     */
    private $acl;

    public function __construct(ACL $acl, ManageMenuItem $menuItemManager)
    {
        $this->menuItemManager = $menuItemManager;
        $this->acl = $acl;
    }

    /**
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function __invoke(ModelSaveEvent $event)
    {
        if ($this->acl->hasPermission('admin/menus/items/create') === true
            && $this->hasNecessaryMenuItemFormFields($event->getRawData())) {
            $formData = $event->getRawData();

            $data = [
                'mode' => 2,
                'block_id' => $formData['block_id'],
                'parent_id' => (int) $formData['parent_id'],
                'display' => $formData['display'],
                'title' => $formData['menu_item_title'],
                'target' => 1,
            ];

            $this->menuItemManager->manageMenuItem(
                \sprintf($formData['menu_item_uri_pattern'], $event->getEntryId()),
                isset($formData['create_menu_item']) ? $data : []
            );
        }
    }

    private function hasNecessaryMenuItemFormFields(array $formData): bool
    {
        return isset(
            $formData['block_id'],
            $formData['parent_id'],
            $formData['display'],
            $formData['menu_item_title'],
            $formData['menu_item_uri_pattern']
        );
    }
}
