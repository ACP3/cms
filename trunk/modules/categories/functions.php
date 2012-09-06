<?php
/**
 * Categories
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

/**
 * Erstellt den Cache für die Kategorien eines Moduls
 *
 * @param string $module
 *  Das Modul, für welches der Kategorien-Cache erstellt werden soll
 * @return boolean
 */
function setCategoriesCache($module)
{
	$data = ACP3_CMS::$db2->fetchAll('SELECT c.id, c.name, c.picture, c.description FROM ' . DB_PRE . 'categories AS c JOIN ' . DB_PRE . 'modules AS m ON(m.id = c.module_id) WHERE m.name = ? ORDER BY c.name ASC', array($module));
	return ACP3_Cache::create($module, $data, 'categories');
}
/**
 * Bindet die gecacheten Kategorien des jeweiligen Moduls ein
 *
 * @param string $module
 *  Das jeweilige Modul, für welches die Kategorien geholt werden sollen
 * @return array
 */
function getCategoriesCache($module)
{
	if (ACP3_Cache::check($module, 'categories') === false)
		setCategoriesCache($module);

	return ACP3_Cache::output($module, 'categories');
}
/**
 * Überprüft, ob eine Kategorie überhaupt existiert
 *
 * @param integer $category_id
 * @return boolean
 */
function categoriesCheck($category_id)
{
	return ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'categories WHERE id = ?', array($category_id)) == 1 ? true : false;
}
/**
 * Überprüft, ob bereits eine Kategorie mit dem selben Namen existiert
 *
 * @param string $name
 * @param string $module
 * @param integer $category_id
 * @return boolean
 */
function categoriesCheckDuplicate($name, $module, $category_id = '')
{
	return ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'categories AS c JOIN ' . DB_PRE . 'modules AS m ON(m.id = c.module_id) WHERE c.name = ? AND m.name = ? AND c.id != ?', array($name, $module, $category_id)) != 0 ? true : false;
}
/**
 * Erzeugt eine neue Kategorie und gibt ihre ID zurück
 *
 * @param string $name
 * @param string $module
 * @return integer
 */
function categoriesCreate($name, $module)
{
	if (categoriesCheckDuplicate($name, $module) === false) {
		$mod_id = ACP3_CMS::$db2->fetchColumn('SELECT id FROM ' . DB_PRE . 'modules WHERE name = ?', array($module));
		$insert_values = array(
			'id' => '',
			'name' => $name,
			'picture' => '',
			'description' => '',
			'module_id' => $mod_id,
		);
		ACP3_CMS::$db2->beginTransaction();
		try {
			ACP3_CMS::$db2->insert(DB_PRE . 'categories', $insert_values);
			$last_id = ACP3_CMS::$db2->lastInsertId();
			ACP3_CMS::$db2->commit();
			setCategoriesCache($module);
			return $last_id;
		} catch (Exception $e) {
			ACP3_CMS::$db2->rollback();
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
function categoriesList($module, $category_id = '', $category_create = false, $form_field_name = 'cat', $custom_text = '') {
	$categories = array();
	$data = getCategoriesCache($module);
	$c_data = count($data);

	$categories['custom_text'] = !empty($custom_text) ? $custom_text : ACP3_CMS::$lang->t('system', 'pls_select');
	$categories['name'] = $form_field_name;
	if ($c_data > 0) {
		for ($i = 0; $i < $c_data; ++$i) {
			$data[$i]['selected'] = selectEntry('cat', $data[$i]['id'], $category_id);
		}
		$categories['categories'] = $data;
	} else {
		$categories['categories'] = array();
	}
	if ($category_create === true && ACP3_Modules::check('categories', 'acp_create') === true) {
		$categories['create']['name'] = $form_field_name . '_create';
		$categories['create']['value'] = isset($_POST[$categories['create']['name']]) ? $_POST[$categories['create']['name']] : '';
	}
	ACP3_CMS::$view->assign('categories', $categories);
	return ACP3_CMS::$view->fetchTemplate('categories/create_list.tpl');
}