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

function setCategoriesCache($module)
{
	global $db;

	return cache::create('categories_' . $module, $db->select('id, name, picture, description', 'categories', 'module = \'' . $module . '\'', 'name ASC'));
}
function getCategoriesCache($module)
{
	if (!cache::check('categories_' . $module))
		setCategoriesCache($module);

	return cache::output('categories_' . $module);
}
function categoriesList($module, $page, $category = '') {
	if (modules::check($module, $page)) {
		$categories = getCategoriesCache($module);
		$c_categories = count($categories);
		
		if ($c_categories > 0) {
			for ($i = 0; $i < $c_categories; ++$i) {
				$categories[$i]['selected'] = selectEntry('cat', $categories[$i]['id'], $category);
				$categories[$i]['name'] = $categories[$i]['name'];
			}
			return $categories;
		}
	}
	return array();
}
?>