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

$categories = $db->select('id, name, description, module', 'categories', 0, 'module ASC, name DESC, id DESC', POS, $auth->entries);
$c_categories = count($categories);

if ($c_categories > 0) {
	$tpl->assign('pagination', pagination($db->countRows('*', 'categories')));
	for ($i = 0; $i < $c_categories; ++$i) {
		$categories[$i]['name'] = $categories[$i]['name'];
		$categories[$i]['description'] = $categories[$i]['description'];
		$info = modules::parseInfo(db::escape($categories[$i]['module'], 3));
		$categories[$i]['module'] = $info['name'];
	}
	$tpl->assign('categories', $categories);
}
$content = modules::fetchTemplate('categories/adm_list.html');
