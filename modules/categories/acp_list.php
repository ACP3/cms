<?php
/**
 * Categories
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (isset($_POST['entries']) || isset($modules->gen['entries'])) {
	include 'modules/categories/entry.php';
} else {
	$categories = $db->select('id, name, description, module', 'categories', 0, 'module ASC, name DESC, id DESC', POS, CONFIG_ENTRIES);
	$c_categories = count($categories);

	if ($c_categories > 0) {
		$tpl->assign('pagination', pagination($db->select('id', 'categories', 0, 0, 0, 0, 1)));
		for ($i = 0; $i < $c_categories; $i++) {
			$categories[$i]['name'] = $categories[$i]['name'];
			$categories[$i]['description'] = $categories[$i]['description'];
			$info = $modules->parseInfo($db->escape($categories[$i]['module'], 3));
			$categories[$i]['module'] = $info['name'];
		}
		$tpl->assign('categories', $categories);
	}
	$content = $tpl->fetch('categories/adm_list.html');
}
?>