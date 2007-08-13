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
if (!$modules->check())
	redirect('errors/403');
if (!empty($modules->id) && $db->select('id', 'access', 'id = \'' . $modules->id . '\'', 0, 0, 0, 1) == '1') {
	if (isset($_POST['submit'])) {
		include 'modules/access/entry.php';
	}
	if (!isset($_POST['submit']) || isset($error_msg)) {
		$tpl->assign('error_msg', isset($error_msg) ? $error_msg : '');

		$access = $db->select('name, mods', 'access', 'id = \'' . $modules->id . '\'');

		$tpl->assign('form', isset($form) ? $form : $access[0]);

		$mods = $db->select('module', 'modules', 'active = \'1\'');
		$c_mods = count($mods);
		$mods_arr = explode('|', $access[0]['mods']);

		for($i = 0; $i < $c_mods; $i++) {
			$mods[$i]['module'] = $db->escape($mods[$i]['module'], 3);
			if ($modules->check(1, $mods[$i]['module'], 'adm_list')) {
				include('modules/' . $mods[$i]['module'] . '/info.php');
				$name = $mod_info['name'];
				$mod_list[$name]['dir'] = $mods[$i]['module'];
				$mod_list[$name]['selected'] = select_entry('modules', $mods[$i]['module'], $mods_arr);
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