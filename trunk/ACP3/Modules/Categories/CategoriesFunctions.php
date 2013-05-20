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

abstract class CategoriesFunctions {

	/**
	 * Erstellt den Cache für die Kategorien eines Moduls
	 *
	 * @param string $module
	 *  Das Modul, für welches der Kategorien-Cache erstellt werden soll
	 * @return boolean
	 */
	public static function setCategoriesCache($module) {
		$data = Core\Registry::get('Db')->fetchAll('SELECT c.id, c.title, c.picture, c.description FROM ' . DB_PRE . 'categories AS c JOIN ' . DB_PRE . 'modules AS m ON(m.id = c.module_id) WHERE m.name = ? ORDER BY c.title ASC', array($module));
		return Core\Cache::create($module, $data, 'categories');
	}

	/**
	 * Gibt die gecacheten Kategorien des jeweiligen Moduls zurück
	 *
	 * @param string $module
	 *  Das jeweilige Modul, für welches die Kategorien geholt werden sollen
	 * @return array
	 */
	public static function getCategoriesCache($module) {
		if (Core\Cache::check($module, 'categories') === false)
			self::setCategoriesCache($module);

		return Core\Cache::output($module, 'categories');
	}

	/**
	 * Überprüft, ob eine Kategorie existiert
	 *
	 * @param integer $category_id
	 * @return boolean
	 */
	public static function categoriesCheck($category_id) {
		return Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'categories WHERE id = ?', array($category_id)) == 1 ? true : false;
	}

	/**
	 * Überprüft, ob bereits eine Kategorie mit dem selben Namen existiert
	 *
	 * @param string $title
	 * @param string $module
	 * @param integer $category_id
	 * @return boolean
	 */
	public static function categoriesCheckDuplicate($title, $module, $category_id = '') {
		return Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'categories AS c JOIN ' . DB_PRE . 'modules AS m ON(m.id = c.module_id) WHERE c.title = ? AND m.name = ? AND c.id != ?', array($title, $module, $category_id)) != 0 ? true : false;
	}

	/**
	 * Erzeugt eine neue Kategorie und gibt ihre ID zurück
	 *
	 * @param string $title
	 * @param string $module
	 * @return integer
	 */
	public static function categoriesCreate($title, $module) {
		if (self::categoriesCheckDuplicate($title, $module) === false) {
			$mod_id = Core\Registry::get('Db')->fetchColumn('SELECT id FROM ' . DB_PRE . 'modules WHERE name = ?', array($module));
			$insert_values = array(
				'id' => '',
				'title' => Core\Functions::str_encode($title),
				'picture' => '',
				'description' => '',
				'module_id' => $mod_id,
			);
			Core\Registry::get('Db')->beginTransaction();
			try {
				Core\Registry::get('Db')->insert(DB_PRE . 'categories', $insert_values);
				$last_id = Core\Registry::get('Db')->lastInsertId();
				Core\Registry::get('Db')->commit();
				self::setCategoriesCache($module);
				return $last_id;
			} catch (\Exception $e) {
				Core\Registry::get('Db')->rollback();
			}
		}
		return 0;
	}

	/**
	 * Listet alle Kategorien eines Moduls auf
	 *
	 * @param string $module
	 * @param string $category_id
	 * @param boolean $category_create
	 * @param string $form_field_name
	 * @return string
	 */
	public static function categoriesList($module, $category_id = '', $category_create = false, $form_field_name = 'cat', $custom_text = '') {
		$categories = array();
		$data = self::getCategoriesCache($module);
		$c_data = count($data);

		$categories['custom_text'] = !empty($custom_text) ? $custom_text : Core\Registry::get('Lang')->t('system', 'pls_select');
		$categories['name'] = $form_field_name;
		if ($c_data > 0) {
			for ($i = 0; $i < $c_data; ++$i) {
				$data[$i]['selected'] = Core\Functions::selectEntry('cat', $data[$i]['id'], $category_id);
			}
			$categories['categories'] = $data;
		} else {
			$categories['categories'] = array();
		}
		if ($category_create === true && Core\Modules::check('categories', 'acp_create') === true) {
			$categories['create']['name'] = $form_field_name . '_create';
			$categories['create']['value'] = isset($_POST[$categories['create']['name']]) ? $_POST[$categories['create']['name']] : '';
		}
		Core\Registry::get('View')->assign('categories', $categories);
		return Core\Registry::get('View')->fetchTemplate('categories/create_list.tpl');
	}

}