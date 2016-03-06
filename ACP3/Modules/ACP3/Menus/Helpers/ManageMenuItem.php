<?php
namespace ACP3\Modules\ACP3\Menus\Helpers;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus\Model\MenuItemRepository;

/**
 * Class ManageMenuItem
 * @package ACP3\Modules\ACP3\Menus\Helpers
 */
class ManageMenuItem
{
    /**
     * @var \ACP3\Core\NestedSet
     */
    protected $nestedSet;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\MenuItemRepository
     */
    protected $menuItemRepository;

    /**
     * @param \ACP3\Core\NestedSet                              $nestedSet
     * @param \ACP3\Modules\ACP3\Menus\Model\MenuItemRepository $menuItemRepository
     */
    public function __construct(
        Core\NestedSet $nestedSet,
        MenuItemRepository $menuItemRepository
    ) {
        $this->nestedSet = $nestedSet;
        $this->menuItemRepository = $menuItemRepository;
    }

    /**
     * @param string $menuItemUri
     * @param bool   $createOrUpdateMenuItem
     * @param array  $data
     *
     * @return bool
     */
    public function manageMenuItem($menuItemUri, $createOrUpdateMenuItem, array $data = [])
    {
        $menuItem = $this->menuItemRepository->getOneMenuItemByUri($menuItemUri);

        if ($createOrUpdateMenuItem === true) {
            // Create a new menu item
            if (empty($menuItem)) {
                $insertValues = [
                    'id' => '',
                    'mode' => $data['mode'],
                    'block_id' => $data['block_id'],
                    'parent_id' => (int)$data['parent_id'],
                    'display' => $data['display'],
                    'title' => $this->get('core.helpers.secure')->strEncode($data['title']),
                    'uri' => $menuItemUri,
                    'target' => $data['target'],
                ];

                return $this->nestedSet->insertNode(
                    (int)$data['parent_id'],
                    $insertValues,
                    MenuItemRepository::TABLE_NAME,
                    true
                ) !== false;
            } else { // Update an existing menu item
                $updateValues = [
                    'block_id' => $data['block_id'],
                    'parent_id' => (int)$data['parent_id'],
                    'display' => $data['display'],
                    'title' => $this->get('core.helpers.secure')->strEncode($data['title'])
                ];

                return $this->nestedSet->editNode(
                    $menuItem['id'],
                    (int)$data['parent_id'],
                    (int)$data['block_id'],
                    $updateValues,
                    MenuItemRepository::TABLE_NAME,
                    true
                );
            }
        } elseif (!empty($menuItem)) { // Delete an existing menu item
            return $this->nestedSet->deleteNode(
                $menuItem['id'],
                MenuItemRepository::TABLE_NAME,
                true
            );
        }

        return true;
    }
}
