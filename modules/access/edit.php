<?php
/**
 * Access
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (!empty($modules->id) && $db->select('id', 'access', 'id = \'' . $modules->id . '\'', 0, 0, 0, 1) == '1') {
	if (isset($_POST['submit'])) {
		include 'modules/access/entry.php';
	}
	if (!isset($_POST['submit']) || isset($error_msg)) {
		$tpl->assign('error_msg', isset($error_msg) ? $error_msg : '');

		$access = $db->select('name, modules', 'access', 'id = \'' . $modules->id . '\'');

		$tpl->assign('form', isset($form) ? $form : $access[0]);

		$active_modules = $modules->active_modules();
		$c_active_modules = count($active_modules);
		$mods_arr = explode(',', $access[0]['modules']);

		// TODO: Selektion der Einträge
		for ($i = 0; $i < $c_active_modules; $i++) {
			if ($active_modules[$i] != 'errors') {
				$mod_info = array();
				include 'modules/' . $active_modules[$i] . '/info.php';
				$name = $mod_info['name'];
				$mod_list[$name]['module'] = $active_modules[$i];
				$mod_list[$name]['name'] = $name;
			}
		}
		ksort($mod_list);
		$tpl->assign('mod_list', $mod_list);

		$content = $tpl->fetch('access/edit.html');
	}
} else {
	redirect('errors/404');
}
?>