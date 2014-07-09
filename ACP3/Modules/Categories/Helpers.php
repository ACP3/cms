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

class Helpers
{
    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;
    /**
     * @var Cache
     */
    protected $cache;
    /**
     * @var Model
     */
    protected $model;
    /**
     * @var Core\Modules
     */
    protected $modules;

    public function __construct(Core\Lang $lang, Core\Modules $modules, Core\View $view, Cache $cache)
    {
        $this->lang = $lang;
        $this->modules = $modules;
        $this->view = $view;
        $this->cache = $cache;
    }

    /**
     * Überprüft, ob eine Kategorie existiert
     *
     * @param integer $categoryId
     * @return boolean
     */
    public function categoryExists($categoryId)
    {
        return $this->model->resultExists($categoryId);
    }

    /**
     * Überprüft, ob bereits eine Kategorie mit dem selben Namen existiert
     *
     * @param string $title
     * @param string $module
     * @param int|string $categoryId
     * @return boolean
     */
    public function categoryIsDuplicate($title, $module, $categoryId = '')
    {
        return $this->model->resultIsDuplicate($title, $module, $categoryId);
    }

    /**
     * Erzeugt eine neue Kategorie und gibt ihre ID zurück
     *
     * @param string $title
     * @param string $module
     * @return integer
     */
    public function categoriesCreate($title, $module)
    {
        if ($this->categoryIsDuplicate($title, $module) === false) {
            $moduleInfo = $this->modules->getModuleInfo($module);

            $insertValues = array(
                'id' => '',
                'title' => Core\Functions::strEncode($title),
                'picture' => '',
                'description' => '',
                'module_id' => $moduleInfo['id'],
            );
            $result = $this->model->insert($insertValues);

            $this->cache->setCache($module);

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
     * @param string $customText
     * @return string
     */
    public function categoriesList($module, $categoryId = '', $categoryCreate = false, $formFieldName = 'cat', $customText = '')
    {
        $categories = array();
        $data = $this->cache->getCache($module);
        $c_data = count($data);

        $categories['custom_text'] = !empty($customText) ? $customText : $this->lang->t('system', 'pls_select');
        $categories['name'] = $formFieldName;
        if ($c_data > 0) {
            for ($i = 0; $i < $c_data; ++$i) {
                $data[$i]['selected'] = Core\Functions::selectEntry('cat', $data[$i]['id'], $categoryId);
            }
            $categories['categories'] = $data;
        } else {
            $categories['categories'] = array();
        }
        if ($categoryCreate === true && $this->modules->hasPermission('admin/categories/index/create') === true) {
            $categories['create']['name'] = $formFieldName . '_create';
            $categories['create']['value'] = isset($_POST[$categories['create']['name']]) ? $_POST[$categories['create']['name']] : '';
        }
        $this->view->assign('categories', $categories);
        return $this->view->fetchTemplate('categories/create_list.tpl');
    }

}
