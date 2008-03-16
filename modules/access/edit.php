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
		$form = $_POST['form'];

		if (empty($form['name']))
			$errors[] = lang('common', 'name_to_short');
		if (!empty($form['name']) && $db->select('id', 'access', 'id != \'' . $modules->id . '\' AND name = \'' . $db->escape($form['name']) . '\'', 0, 0, 0, 1) == '1')
			$errors[] = lang('access', 'access_level_already_exist');
		// Überprüfen, ob zumindest einem Modul ein Zugriffslevel zugewiesen wurde
		$empty = true;
		foreach ($form['modules'] as $key) {
			if (!empty($key)) {
				$empty = false;
				break;
			}
		}
		if ($empty)
			$errors[] = lang('access', 'select_modules');

		if (isset($errors)) {
			$tpl->assign('error_msg', combo_box($errors));
		} else {
			// String für die einzelnen Zugriffslevel auf die Module erstellen
			$form['modules']['errors'] = '2';
			ksort($form['modules']);
			$insert_mods = '';

			foreach ($form['modules'] as $module => $level) {
				$insert_mods.= $module . ':' . $level . ',';
			}

			$update_values = array(
				'name' => $db->escape($form['name']),
				'modules' => substr($insert_mods, 0, -1),
			);

			$bool = $db->update('access', $update_values, 'id = \'' . $modules->id . '\'');

			$content = combo_box($bool ? lang('access', 'edit_success') : lang('access', 'edit_error'), uri('acp/access'));
		}
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$access = $db->select('name, modules', 'access', 'id = \'' . $modules->id . '\'');

		$tpl->assign('form', isset($form) ? $form : $access[0]);

		$mod_list = $modules->modulesList();
		$mods_arr = explode(',', $access[0]['modules']);
		$c_mods_arr = count($mods_arr);

		function select_level($dir, $db_value, $value)
		{
			if (isset($_POST['form']['modules'][$dir])) {
				return $_POST['form']['modules'][$dir] == $value ? ' selected="selected"' : '';
			} elseif ($db_value == $value) {
				return ' selected="selected"';
			}
			return '';
		}

		foreach ($mod_list as $name => $info) {
			if ($info['dir'] == 'errors' || !$info['active']) {
				unset($mod_list[$name]);
			} else {
				for ($i = 0; $i < $c_mods_arr; $i++) {
					if ($info['dir'] == substr($mods_arr[$i], 0, -2)) {
						$db_value = substr($mods_arr[$i], -1, 1);
						$mod_list[$name]['level_0_selected'] = select_level($info['dir'], $db_value, '0');
						$mod_list[$name]['level_1_selected'] = select_level($info['dir'], $db_value, '1');
						$mod_list[$name]['level_2_selected'] = select_level($info['dir'], $db_value, '2');
						break;
					}
				}
			}
		}
		$tpl->assign('mod_list', $mod_list);

		$content = $tpl->fetch('access/edit.html');
	}
} else {
	redirect('errors/404');
}
?>