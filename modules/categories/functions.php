<?php
/**
 * Categories
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit();

/**
 * Erstellt den Cache für die Kategorien eines Moduls
 *
 * @param string $module
 *  Das Modul, für welches der Kategorien-Cache erstellt werden soll
 * @return boolean
 */
function setCategoriesCache($module)
{
	global $db;

	$data = ACP3_CMS::$db->query('SELECT c.id, c.name, c.picture, c.description FROM {pre}categories AS c JOIN {pre}modules AS m ON(m.id = c.module_id) WHERE m.name = \'' . $module . '\' ORDER BY c.name ASC');
	return ACP3_Cache::create('categories_' . $module, $data);
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
	if (ACP3_Cache::check('categories_' . $module) === false)
		setCategoriesCache($module);

	return ACP3_Cache::output('categories_' . $module);
}
/**
 * Überprüft, ob eine Kategorie überhaupt existiert
 *
 * @param integer $category_id
 * @return boolean
 */
function categoriesCheck($category_id)
{
	global $db;

	return ACP3_CMS::$db->countRows('id', 'categories', 'id = \'' . ACP3_CMS::$db->escape($category_id) . '\'') == 1 ? true : false;
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
	global $db;

	$id = ACP3_Validate::isNumber($category_id) ? ' AND id != \'' . $category_id . '\'' : '';
	return ACP3_CMS::$db->query('SELECT COUNT(c.*) FROM {pre}categories AS c JOIN {pre}modules AS m ON(m.id = c.module_id) WHERE c.name = \'' . ACP3_CMS::$db->escape($name) . '\' AND m.name = \'' . ACP3_CMS::$db->escape($module) . '\'' . $id) != 0 ? true : false;
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
	global $db;

	if (categoriesCheckDuplicate($name, $module) === false) {
		$mod_id = ACP3_CMS::$db->select('id', 'modules', 'name = \'' . ACP3_CMS::$db->escape($module) . '\'');
		$insert_values = array(
			'id' => '',
			'name' => ACP3_CMS::$db->escape($name),
			'picture' => '',
			'description' => '',
			'module_id' => $mod_id[0]['id'],
		);
		ACP3_CMS::$db->link->beginTransaction();
		ACP3_CMS::$db->insert('categories', $insert_values);
		$last_id = ACP3_CMS::$db->link->lastInsertId();
		ACP3_CMS::$db->link->commit();
		setCategoriesCache($module);

		return $last_id;
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
	global $db, $lang, $tpl;

	$categories = array();
	$data = getCategoriesCache($module);
	$c_data = count($data);

	$categories['custom_text'] = !empty($custom_text) ? $custom_text : ACP3_CMS::$lang->t('common', 'pls_select');
	$categories['name'] = $form_field_name;
	if ($c_data > 0) {
		for ($i = 0; $i < $c_data; ++$i) {
			$data[$i]['selected'] = selectEntry('cat', $data[$i]['id'], $category_id);
			$data[$i]['name'] = ACP3_CMS::$db->escape($data[$i]['name'], 3);
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