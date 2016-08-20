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
     * @var \ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemRepository
     */
    protected $menuItemRepository;
    /**
     * @var Cache
     */
    protected $menusCache;
    /**
     * @var Core\NestedSet\Operation\Delete
     */
    protected $deleteOperation;
    /**
     * @var Core\NestedSet\Operation\Insert
     */
    protected $insertOperation;
    /**
     * @var Core\NestedSet\Operation\Edit
     */
    protected $editOperation;

    /**
     * @param \ACP3\Core\Helpers\Secure $secureHelper
     * @param Core\NestedSet\Operation\Insert $insertOperation
     * @param Core\NestedSet\Operation\Edit $editOperation
     * @param Core\NestedSet\Operation\Delete $deleteOperation
     * @param \ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemRepository $menuItemRepository
     * @param Cache $menusCache
     */
    public function __construct(
        Core\Helpers\Secure $secureHelper,
        Core\NestedSet\Operation\Insert $insertOperation,
        Core\NestedSet\Operation\Edit $editOperation,
        Core\NestedSet\Operation\Delete $deleteOperation,
        MenuItemRepository $menuItemRepository,
        Cache $menusCache
    ) {
        $this->secureHelper = $secureHelper;
        $this->menuItemRepository = $menuItemRepository;
        $this->menusCache = $menusCache;
        $this->deleteOperation = $deleteOperation;
        $this->insertOperation = $insertOperation;
        $this->editOperation = $editOperation;
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
            $result = $this->deleteOperation->execute($menuItem['id']);
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

        return $this->insertOperation->execute($insertValues, (int)$data['parent_id']) !== false;
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

        return $this->editOperation->execute(
            $menuItem['id'], (int)$data['parent_id'], (int)$data['block_id'], $updateValues
        );
    }
}
