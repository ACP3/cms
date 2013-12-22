<?php

/**
 * Categories
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

namespace ACP3\Modules\Categories;

use ACP3\Core;

abstract class Helpers
{

    /**
     *
     * @var Model
     */
    protected static $model;

    protected static function _init()
    {
        if (!self::$model) {
            self::$model = new Model(Core\Registry::get('Db'));
        }
    }

    /**
     * Überprüft, ob eine Kategorie existiert
     *
     * @param integer $categoryId
     * @return boolean
     */
    public static function categoryExists($categoryId)
    {
        self::_init();
        return self::$model->resultExists($categoryId);
    }

    /**
     * Überprüft, ob bereits eine Kategorie mit dem selben Namen existiert
     *
     * @param string $title
     * @param string $module
     * @param integer $categoryId
     * @return boolean
     */
    public static function categoryIsDuplicate($title, $module, $categoryId = '')
    {
        self::_init();
        return self::$model->resultIsDuplicate($title, $module, $categoryId);
    }

    /**
     * Erzeugt eine neue Kategorie und gibt ihre ID zurück
     *
     * @param string $title
     * @param string $module
     * @return integer
     */
    public static function categoriesCreate($title, $module)
    {
        if (self::categoryIsDuplicate($title, $module) === false) {
            $moduleId = Core\Registry::get('Db')->fetchColumn('SELECT id FROM ' . DB_PRE . 'modules WHERE name = ?', array($module));

            $insertValues = array(
                'id' => '',
                'title' => Core\Functions::strEncode($title),
                'picture' => '',
                'description' => '',
                'module_id' => $moduleId,
            );
            $result = self::$model->insert($insertValues);

            self::$model->setCache($module);

            return $result;
        }
        return 0;
    }

    /**
     * Listet alle Kategorien eines Moduls auf
     *
     * @param string $module
     * @param string $categoryId
     * @param boolean $categoryCreate
     * @param string $formFieldName
     * @return string
     */
    public static function categoriesList($module, $categoryId = '', $categoryCreate = false, $formFieldName = 'cat', $customText = '')
    {
        self::_init();

        $categories = array();
        $data = self::$model->getCache($module);
        $c_data = count($data);

        $categories['custom_text'] = !empty($customText) ? $customText : Core\Registry::get('Lang')->t('system', 'pls_select');
        $categories['name'] = $formFieldName;
        if ($c_data > 0) {
            for ($i = 0; $i < $c_data; ++$i) {
                $data[$i]['selected'] = Core\Functions::selectEntry('cat', $data[$i]['id'], $categoryId);
            }
            $categories['categories'] = $data;
        } else {
            $categories['categories'] = array();
        }
        if ($categoryCreate === true && Core\Modules::hasPermission('categories', 'acp_create') === true) {
            $categories['create']['name'] = $formFieldName . '_create';
            $categories['create']['value'] = isset($_POST[$categories['create']['name']]) ? $_POST[$categories['create']['name']] : '';
        }
        Core\Registry::get('View')->assign('categories', $categories);
        return Core\Registry::get('View')->fetchTemplate('categories/create_list.tpl');
    }

}
