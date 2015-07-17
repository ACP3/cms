<?php
namespace ACP3\Modules\ACP3\Menus;

use ACP3\Core;

/**
 * Class Helpers
 * @package ACP3\Modules\ACP3\Menus
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
     * @var \ACP3\Core\Lang
     */
    protected $lang;
    /**
     * @var \ACP3\Core\NestedSet
     */
    protected $nestedSet;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model
     */
    protected $menusModel;
    /**
     * @var \ACP3\Modules\ACP3\Menus\Cache
     */
    protected $menusCache;

    /**
     * @param \ACP3\Core\Lang                $lang
     * @param \ACP3\Core\NestedSet           $nestedSet
     * @param \ACP3\Core\Helpers\Forms       $formsHelper
     * @param \ACP3\Modules\ACP3\Menus\Model $menusModel
     * @param \ACP3\Modules\ACP3\Menus\Cache $menusCache
     */
    public function __construct(
        Core\Lang $lang,
        Core\NestedSet $nestedSet,
        Core\Helpers\Forms $formsHelper,
        Model $menusModel,
        Cache $menusCache
    )
    {
        $this->lang = $lang;
        $this->nestedSet = $nestedSet;
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
            $this->menuItems = $this->menusCache->getMenusCache();
        }

        $output = [];

        if (count($this->menuItems) > 0) {
            foreach ($this->menuItems as $row) {
                if (!($row['left_id'] >= $leftId && $row['right_id'] <= $rightId)) {
                    $row['selected'] = $this->formsHelper->selectEntry('parent_id', $row['id'], $parentId);
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
        $lang_display = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];

        return [
            'blocks' => $this->menusDropdown($blockId),
            'display' => $this->formsHelper->selectGenerator('display', [1, 0], $lang_display, $displayMenuItem, 'checked'),
            'menuItems' => $this->menuItemsList($parentId, $leftId, $rightId)
        ];
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
        $menuItem = $this->menusModel->getOneMenuItemUri($menuItemUri);

        if ($createOrUpdateMenuItem === true) {
            // Create a new menu item
            if (empty($menuItem)) {
                $insertValues = [
                    'id' => '',
                    'mode' => $data['mode'],
                    'block_id' => $data['block_id'],
                    'parent_id' => (int)$data['parent_id'],
                    'display' => $data['display'],
                    'title' => Core\Functions::strEncode($data['title']),
                    'uri' => $menuItemUri,
                    'target' => $data['target'],
                ];

                return $this->nestedSet->insertNode(
                    (int)$data['parent_id'],
                    $insertValues,
                    Model::TABLE_NAME_ITEMS,
                    true
                ) !== false;
            } else { // Update an existing menu item
                $updateValues = [
                    'block_id' => $data['block_id'],
                    'parent_id' => (int)$data['parent_id'],
                    'display' => $data['display'],
                    'title' => Core\Functions::strEncode($data['title'])
                ];

                return $this->nestedSet->editNode(
                    $menuItem['id'],
                    (int)$data['parent_id'],
                    (int)$data['block_id'],
                    $updateValues,
                    Model::TABLE_NAME_ITEMS,
                    true
                );
            }
        } elseif (!empty($menuItem)) { // Delete an existing menu item
            return $this->nestedSet->deleteNode(
                $menuItem['id'],
                Model::TABLE_NAME_ITEMS,
                true
            );
        }

        return true;
    }
}
