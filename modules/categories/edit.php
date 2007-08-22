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

if (!empty($modules->id) && $db->select('id', 'categories', 'id = \'' . $modules->id . '\'', 0, 0, 0, 1) == '1') {
	if (isset($_POST['submit'])) {
		include 'modules/categories/entry.php';
	}
	if (!isset($_POST['submit']) || isset($error_msg)) {
		$tpl->assign('error_msg', isset($error_msg) ? $error_msg : '');

		$category = $db->select('name, description, module', 'categories', 'id = \'' . $modules->id . '\'');

		$tpl->assign('form', isset($form) ? $form : $category[0]);

		$mod_list = $modules->modulesList();

		foreach ($mod_list as $name => $info) {
			if ($info['active'] && $info['categories']) {
				$mod_list[$name]['selected'] = select_entry('module', $info['dir'], $db->escape($category[0]['module'], 3));
			} else {
				unset($mod_list[$name]);
			}
		}
		$tpl->assign('mod_list', $mod_list);

		$content = $tpl->fetch('categories/edit.html');
	}
} else {
	redirect('errors/404');
}
?>