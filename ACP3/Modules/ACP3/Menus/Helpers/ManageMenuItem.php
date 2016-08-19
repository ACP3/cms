<?php
namespace ACP3\Modules\ACP3\Menus\Helpers;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus\Cache;
use ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemRepository;

/**
 * Class ManageMenuItem
 * @package ACP3\Modules\ACP3\Menus\Helpers
 */
class ManageMenuItem
{
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var \ACP3\Core\NestedSet\NestedSet
     */
    protected $nestedSet;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemRepository
     */
    protected $menuItemRepository;
    /**
     * @var Cache
     */
    protected $menusCache;

    /**
     * @param \ACP3\Core\Helpers\Secure $secureHelper
     * @param \ACP3\Core\NestedSet\NestedSet $nestedSet
     * @param \ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemRepository $menuItemRepository
     * @param Cache $menusCache
     */
    public function __construct(
        Core\Helpers\Secure $secureHelper,
        Core\NestedSet\NestedSet $nestedSet,
        MenuItemRepository $menuItemRepository,
        Cache $menusCache
    ) {
        $this->secureHelper = $secureHelper;
        $this->nestedSet = $nestedSet;
        $this->menuItemRepository = $menuItemRepository;
        $this->menusCache = $menusCache;
    }

    /**
     * @param string $menuItemUri
     * @param bool $createOrUpdateMenuItem
     * @param array $data
     *
     * @return bool
     */
    public function manageMenuItem($menuItemUri, $createOrUpdateMenuItem, array $data = [])
    {
        $menuItem = $this->menuItemRepository->getOneMenuItemByUri($menuItemUri);
        $result = true;

        if ($createOrUpdateMenuItem === true) {
            if (empty($menuItem)) {
                $result = $this->createMenuItem($data, $menuItemUri);
            } else {
                $result = $this->updateMenuItem($data, $menuItem);
            }
        } elseif (!empty($menuItem)) {
            $result = $this->nestedSet->deleteNode(
                $menuItem['id'],
                MenuItemRepository::TABLE_NAME,
                true
            );
        }

        $this->menusCache->saveMenusCache();

        return $result;
    }

    /**
     * @param array $data
     * @param string $menuItemUri
     * @return bool
     */
    protected function createMenuItem(array $data, $menuItemUri)
    {
        $insertValues = [
            'id' => '',
            'mode' => $data['mode'],
            'block_id' => $data['block_id'],
            'parent_id' => (int)$data['parent_id'],
            'display' => $data['display'],
            'title' => $this->secureHelper->strEncode($data['title']),
            'uri' => $menuItemUri,
            'target' => $data['target'],
        ];

        return $this->nestedSet->insertNode(
            (int)$data['parent_id'],
            $insertValues,
            MenuItemRepository::TABLE_NAME,
            true
        ) !== false;
    }

    /**
     * @param array $data
     * @param array $menuItem
     * @return bool
     */
    protected function updateMenuItem(array $data, array $menuItem)
    {
        $updateValues = [
            'block_id' => $data['block_id'],
            'parent_id' => (int)$data['parent_id'],
            'display' => $data['display'],
            'title' => $this->secureHelper->strEncode($data['title'])
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
}
