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

		$mods = $db->select('module', 'modules', 'active = \'1\'');
		$c_mods = count($mods);

		for ($i = 0; $i < $c_mods; $i++) {
			$mods[$i]['module'] = $db->escape($mods[$i]['module'], 3);
			if ($modules->is_active($mods[$i]['module'])) {
				include('modules/' . $mods[$i]['module'] . '/info.php');
				if (isset($mod_info['categories'])) {
					$name = $mod_info['name'];
					$mod_list[$name]['dir'] = $mods[$i]['module'];
					$mod_list[$name]['selected'] = select_entry('modules', $mods[$i]['module'], $db->escape($category[0]['module'], 3));
					$mod_list[$name]['name'] = $name;
				}
			}
		}
		ksort($mod_list);
		$tpl->assign('mod_list', $mod_list);

		$content = $tpl->fetch('categories/edit.html');
	}
} else {
	redirect('errors/404');
}
?>