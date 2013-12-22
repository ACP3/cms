<?php
/**
 * Created by PhpStorm.
 * User: goratsch
 * Date: 22.12.13
 * Time: 17:00
 */

namespace ACP3\Modules\Menus;


use ACP3\Core;

/**
 * Description of Model
 *
 * @author Tino Goratsch
 */
class Model extends Core\Model
{

    const TABLE_NAME = 'menus';
    const TABLE_NAME_ITEMS = 'menu_items';

    public function __construct(\Doctrine\DBAL\Connection $db)
    {
        parent::__construct($db);
    }

    public function menuExists($id)
    {
        return (int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = :id', array('id' => $id)) > 0 ? true : false;
    }

    public function menuExistsByName($indexName, $id = 0)
    {
        $where = !empty($id) ? ' AND id != :id' : '';
        return (int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE index_name = :indexName' . $where, array('indexName' => $indexName, 'id' => $id)) > 0 ? true : false;
    }

    public function menuItemExists($id)
    {
        return (int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->prefix . static::TABLE_NAME_ITEMS . ' WHERE id = :id', array('id' => $id)) > 0 ? true : false;
    }

    public function getOneById($id)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = ?', array($id));
    }

    public function getOneMenuItemById($id)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->prefix . static::TABLE_NAME_ITEMS . ' WHERE id = ?', array($id));
    }

    public function countAll()
    {
        return count($this->getAll());
    }

    public function getAllItemsByBlockId($blockId)
    {
        return $this->db->fetchAll('SELECT id FROM ' . $this->prefix . static::TABLE_NAME_ITEMS . ' WHERE block_id = ?', array($blockId));
    }

    public function getMenuNameById($id)
    {
        return $this->db->fetchColumn('SELECT index_name FROM ' . $this->prefix . static::TABLE_NAME . ' WHERE id = ?', array($id));
    }

    public function getMenuItemUriById($id)
    {
        return $this->db->fetchColumn('SELECT uri FROM ' . $this->prefix . static::TABLE_NAME_ITEMS . ' WHERE id = ?', array($id));
    }

    public function getMenuItemBlockIdById($id)
    {
        return $this->db->fetchColumn('SELECT block_id FROM ' . $this->prefix . static::TABLE_NAME_ITEMS . ' WHERE id = ?', array($id));
    }

    public function getMenuItemIdByUri($uri)
    {
        return $this->db->fetchColumn('SELECT id FROM ' . $this->prefix . static::TABLE_NAME_ITEMS . ' WHERE uri = ?', array($uri));
    }

    public function getAllMenus($limitStart = '', $resultsPerPage = '')
    {
        $limitStmt = $this->_buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll('SELECT * FROM ' . $this->prefix . static::TABLE_NAME . ' ORDER BY title ASC, id ASC' . $limitStmt);
    }

    public function validateCreate(array $formData, \ACP3\Core\Lang $lang)
    {
        $this->validateFormKey($lang);

        $errors = array();
        if (!preg_match('/^[a-zA-Z]+\w/', $formData['index_name']))
            $errors['index-name'] = $lang->t('menus', 'type_in_index_name');
        if (!isset($errors) && $this->menuExistsByName($formData['index_name']) === true)
            $errors['index-name'] = $lang->t('menus', 'index_name_unique');
        if (strlen($formData['title']) < 3)
            $errors['title'] = $lang->t('menus', 'menu_bar_title_to_short');


        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    public function validateItem(array $formData, \ACP3\Core\Lang $lang)
    {
        $this->validateFormKey($lang);

        $errors = array();
        if (Core\Validate::isNumber($formData['mode']) === false)
            $errors['mode'] = $lang->t('menus', 'select_page_type');
        if (strlen($formData['title']) < 3)
            $errors['title'] = $lang->t('menus', 'title_to_short');
        if (Core\Validate::isNumber($formData['block_id']) === false)
            $errors['block-id'] = $lang->t('menus', 'select_menu_bar');
        if (!empty($formData['parent']) && Core\Validate::isNumber($formData['parent']) === false)
            $errors['parent'] = $lang->t('menus', 'select_superior_page');
        if (!empty($formData['parent']) && Core\Validate::isNumber($formData['parent']) === true) {
            // Überprüfen, ob sich die ausgewählte übergeordnete Seite im selben Block befindet
            $parent_block = $this->getMenuItemBlockIdById($formData['parent']);
            if (!empty($parent_block) && $parent_block != $formData['block_id'])
                $errors['parent'] = $lang->t('menus', 'superior_page_not_allowed');
        }
        if ($formData['display'] != 0 && $formData['display'] != 1)
            $errors[] = $lang->t('menus', 'select_item_visibility');
        if (Core\Validate::isNumber($formData['target']) === false ||
            $formData['mode'] == 1 && Core\Modules::isInstalled($formData['module']) === false ||
            $formData['mode'] == 2 && Core\Validate::isInternalURI($formData['uri']) === false ||
            $formData['mode'] == 3 && empty($formData['uri']) ||
            $formData['mode'] == 4 && (Core\Validate::isNumber($formData['articles']) === false || \ACP3\Modules\Articles\Helpers::articleExists($formData['articles']) === false)
        )
            $errors[] = $lang->t('menus', 'type_in_uri_and_target');
        if ($formData['mode'] == 2 && (bool)CONFIG_SEO_ALIASES === true && !empty($formData['alias']) &&
            (Core\Validate::isUriSafe($formData['alias']) === false || Core\Validate::uriAliasExists($formData['alias']) === true)
        )
            $errors['alias'] = $lang->t('system', 'uri_alias_unallowed_characters_or_exists');


        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }


    public function validateEdit(array $formData, \ACP3\Core\Lang $lang, \ACP3\Core\URI $uri)
    {
        $this->validateFormKey($lang);

        $errors = array();
        if (!preg_match('/^[a-zA-Z]+\w/', $formData['index_name']))
            $errors['index-name'] = $lang->t('menus', 'type_in_index_name');
        if (!isset($errors) && $this->menuExistsByName($formData['index_name'], $uri->id) === true)
            $errors['index-name'] = $lang->t('menus', 'index_name_unique');
        if (strlen($formData['title']) < 3)
            $errors['title'] = $lang->t('menus', 'menu_bar_title_to_short');

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    /**
     * Erstellt den Cache für die Menüpunkte
     *
     * @return boolean
     */
    public function setMenuItemsCache()
    {
        $items = $this->db->fetchAll('SELECT n.*, COUNT(*)-1 AS level, ROUND((n.right_id - n.left_id - 1) / 2) AS children FROM ' . $this->prefix . static::TABLE_NAME_ITEMS . ' AS p, ' . $this->prefix . static::TABLE_NAME_ITEMS . ' AS n WHERE n.left_id BETWEEN p.left_id AND p.right_id GROUP BY n.left_id ORDER BY n.left_id');
        $c_items = count($items);

        if ($c_items > 0) {
            $menus = $this->getAllMenus();
            $c_menus = count($menus);

            for ($i = 0; $i < $c_menus; ++$i) {
                $this->setVisibleMenuItemsCache($menus[$i]['index_name']);
            }

            for ($i = 0; $i < $c_items; ++$i) {
                for ($j = 0; $j < $c_menus; ++$j) {
                    if ($items[$i]['block_id'] == $menus[$j]['id']) {
                        $items[$i]['block_title'] = $menus[$j]['title'];
                        $items[$i]['block_name'] = $menus[$j]['index_name'];
                    }
                }
            }

            $mode_search = array('1', '2', '3', '4');
            $mode_replace = array(
                Core\Registry::get('Lang')->t('menus', 'module'),
                Core\Registry::get('Lang')->t('menus', 'dynamic_page'),
                Core\Registry::get('Lang')->t('menus', 'hyperlink'),
                Core\Registry::get('Lang')->t('menus', 'article')
            );

            for ($i = 0; $i < $c_items; ++$i) {
                $items[$i]['mode_formatted'] = str_replace($mode_search, $mode_replace, $items[$i]['mode']);

                // Bestimmen, ob die Seite die Erste und/oder Letzte eines Knotens ist
                $first = $last = true;
                if ($i > 0) {
                    for ($j = $i - 1; $j >= 0; --$j) {
                        if ($items[$j]['parent_id'] == $items[$i]['parent_id'] && $items[$j]['block_name'] == $items[$i]['block_name']) {
                            $first = false;
                            break;
                        }
                    }
                }

                for ($j = $i + 1; $j < $c_items; ++$j) {
                    if ($items[$i]['parent_id'] == $items[$j]['parent_id'] && $items[$j]['block_name'] == $items[$i]['block_name']) {
                        $last = false;
                        break;
                    }
                }

                $items[$i]['first'] = $first;
                $items[$i]['last'] = $last;
            }
        }
        return Core\Cache::create('items', $items, 'menus');
    }

    /**
     * Bindet die gecacheten Menüpunkte ein
     *
     * @return array
     */
    public function getMenuItemsCache()
    {
        if (Core\Cache::check('items', 'menus') === false) {
            $this->setMenuItemsCache();
        }

        return Core\Cache::output('items', 'menus');
    }

    /**
     * Erstellt den Cache für die Menüpunkte
     *
     * @return boolean
     */
    public function setVisibleMenuItemsCache($block)
    {
        $items = $this->db->fetchAll('SELECT n.*, COUNT(*)-1 AS level, ROUND((n.right_id - n.left_id - 1) / 2) AS children, b.title AS block_title, b.index_name AS block_name FROM ' . $this->prefix . static::TABLE_NAME_ITEMS . ' AS p, ' . $this->prefix . static::TABLE_NAME_ITEMS . ' AS n JOIN ' . $this->prefix . static::TABLE_NAME . ' AS b ON(n.block_id = b.id) WHERE b.index_name = ? AND n.display = 1 AND n.left_id BETWEEN p.left_id AND p.right_id GROUP BY n.left_id ORDER BY n.left_id', array($block));
        return Core\Cache::create('visible_items_' . $block, $items, 'menus');
    }

    /**
     * Bindet die gecacheten Menüpunkte ein
     *
     * @return array
     */
    public function getVisibleMenuItems($block)
    {
        if (Core\Cache::check('visible_items_' . $block, 'menus') === false) {
            $this->setVisibleMenuItemsCache($block);
        }

        return Core\Cache::output('visible_items_' . $block, 'menus');
    }


}
