<?php
namespace ACP3\Modules\ACP3\Menus\Helpers;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus\Model\Repository\MenusRepository;

/**
 * Class MenuItemFormFields
 * @package ACP3\Modules\ACP3\Menus\Helpers
 */
class MenuItemFormFields
{
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\Repository\MenusRepository
     */
    protected $menusModel;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Helpers\MenuItemsList
     */
    protected $menusHelper;

    /**
     * @param \ACP3\Core\Helpers\Forms                      $formsHelper
     * @param \ACP3\Modules\ACP3\Menus\Helpers\MenuItemsList              $menusHelper
     * @param \ACP3\Modules\ACP3\Menus\Model\Repository\MenusRepository $menusModel
     */
    public function __construct(
        Core\Helpers\Forms $formsHelper,
        MenuItemsList $menusHelper,
        MenusRepository $menusModel
    ) {
        $this->formsHelper = $formsHelper;
        $this->menusHelper = $menusHelper;
        $this->menusModel = $menusModel;
    }

    /**
     * Gibt alle Menüleisten zur Benutzung in einem Dropdown-Menü aus
     *
     * @param integer $selected
     *
     * @return array
     */
    protected function menusDropDown($selected = 0)
    {
        $menus = $this->menusModel->getAllMenus();
        $cMenus = count($menus);
        for ($i = 0; $i < $cMenus; ++$i) {
            $menus[$i]['selected'] = $this->formsHelper->selectEntry('block_id', (int)$menus[$i]['id'], (int)$selected);
        }

        return $menus;
    }

    /**
     * @param int $blockId
     * @param int $parentId
     * @param int $leftId
     * @param int $rightId
     * @param int $displayMenuItem
     *
     * @return array
     */
    public function createMenuItemFormFields($blockId = 0, $parentId = 0, $leftId = 0, $rightId = 0, $displayMenuItem = 1)
    {
        return [
            'blocks' => $this->menusDropDown($blockId),
            'display' => $this->formsHelper->yesNoCheckboxGenerator('display', $displayMenuItem),
            'menuItems' => $this->menusHelper->menuItemsList($parentId, $leftId, $rightId)
        ];
    }
}
