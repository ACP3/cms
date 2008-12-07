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
 * Listet alle Kategorien eines Moduls auf
 *
 * @param string $module
 * @param string $category
 * @return array
 */
function categoriesList($module, $category = '') {
	$categories = getCategoriesCache($module);
	$c_categories = count($categories);
		
	if ($c_categories > 0) {
		for ($i = 0; $i < $c_categories; ++$i) {
			$categories[$i]['selected'] = selectEntry('cat', $categories[$i]['id'], $category);
			$categories[$i]['name'] = $categories[$i]['name'];
		}
		return $categories;
	}
	return array();
}
?>