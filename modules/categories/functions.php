<?php
/**
 * Categories
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3') && !defined('IN_ADM'))
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
	return cache::create('categories_' . $module, $db->select('id, name, picture, description', 'categories', 'module = \'' . $module . '\'', 'name ASC'));
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
	if (!cache::check('categories_' . $module))
		setCategoriesCache($module);

	return cache::output('categories_' . $module);
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

	return $db->countRows('id', 'categories', 'id = \'' . db::escape($category_id) . '\'') == 1 ? true : false;
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

	$id = validate::isNumber($category_id) ? ' AND id != \'' . $category_id . '\'' : '';
	return $db->countRows('id', 'categories', 'name = \'' . db::escape($name) . '\' AND module = \'' . db::escape($module) . '\'' . $id) != 0 ? true : false;
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

	if (!categoriesCheckDuplicate($name, $module)) {
		$insert_values = array(
			'id' => '',
			'name' => db::escape($name),
			'picture' => '',
			'description' => '',
			'module' => db::escape($module),
		);
		$db->link->beginTransaction();
		$db->insert('categories', $insert_values);
		$last_id = $db->link->lastInsertId();
		$db->link->commit();
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
 * @param string $category_name
 * @return string
 */
function categoriesList($module, $category_id = '', $category_create = false, $category_name = 'cat') {
	global $tpl;

	$categories = array();
	$data = getCategoriesCache($module);
	$c_data = count($data);
	
	$categories['name'] = $category_name;
	if ($c_data > 0) {
		for ($i = 0; $i < $c_data; ++$i) {
			$data[$i]['selected'] = selectEntry('cat', $data[$i]['id'], $category_id);
			$data[$i]['name'] = $data[$i]['name'];
		}
		$categories['categories'] = $data;
	} else {
		$categories['categories'] = array();
	}
	if ($category_create === true && modules::check('categories', 'create') == 1) {
		$categories['create']['name'] = $category_name . '_create';
		$categories['create']['value'] = isset($_POST['form'][$categories['create']['name']]) ? $_POST['form'][$categories['create']['name']] : '';
	}
	$tpl->assign('categories', $categories);
	return modules::fetchTemplate('categories/create_list.html');
}
