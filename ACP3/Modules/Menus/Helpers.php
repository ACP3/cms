<?php
namespace ACP3\Modules\Menus;

use ACP3\Core;

/**
 * Class Helpers
 * @package ACP3\Modules\Menus
 */
class Helpers
{
    const ARTICLES_URL_KEY_REGEX = '/^(articles\/index\/details\/id_([0-9]+)\/)$/';

    /**
     * @var array
     */
    protected $menuItems = [];
    /**
     * @var array
     */
    protected $navbar = [];
    /**
     * @var Core\Helpers\Forms
     */
    protected $formsHelper;
    /**
     * @var Model
     */
    protected $menusModel;
    /**
     * @var Cache
     */
    protected $menusCache;

    /**
     * @param \ACP3\Core\Helpers\Forms  $formsHelper
     * @param \ACP3\Modules\Menus\Model $menusModel
     * @param \ACP3\Modules\Menus\Cache $menusCache
     */
    public function __construct(
        Core\Helpers\Forms $formsHelper,
        Model $menusModel,
        Cache $menusCache
    ) {
        $this->formsHelper = $formsHelper;
        $this->menusModel = $menusModel;
        $this->menusCache = $menusCache;
    }

    /**
     * Auflistung der Seiten
     *
     * @param integer $parentId
     *  ID des Elternknotens
     * @param integer $leftId
     * @param integer $rightId
     *
     * @return array
     */
    public function menuItemsList($parentId = 0, $leftId = 0, $rightId = 0)
    {
        // Menüpunkte einbinden
        if (empty($this->menuItems)) {
            $this->menuItems = $this->menusCache->getMenuItemsCache();
        }

        $output = [];

        if (count($this->menuItems) > 0) {
            foreach ($this->menuItems as $row) {
                if (!($row['left_id'] >= $leftId && $row['right_id'] <= $rightId)) {
                    $row['selected'] = $this->formsHelper->selectEntry('parent', $row['id'], $parentId);
                    $row['spaces'] = str_repeat('&nbsp;&nbsp;', $row['level']);

                    // Titel für den aktuellen Block setzen
                    $output[$row['block_name']]['title'] = $row['block_title'];
                    $output[$row['block_name']]['menu_id'] = $row['block_id'];
                    $output[$row['block_name']]['items'][] = $row;
                }
            }
        }
        return $output;
    }

    /**
     * Gibt alle Menüleisten zur Benutzung in einem Dropdown-Menü aus
     *
     * @param integer $selected
     *
     * @return array
     */
    public function menusDropdown($selected = 0)
    {
        $menus = $this->menusModel->getAllMenus();
        $c_menus = count($menus);
        for ($i = 0; $i < $c_menus; ++$i) {
            $menus[$i]['selected'] = $this->formsHelper->selectEntry('block_id', (int)$menus[$i]['id'], (int)$selected);
        }

        return $menus;
    }

}
