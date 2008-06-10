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

include_once ACP3_ROOT . 'modules/access/functions.php';

if (validate::isNumber($uri->id) && $db->select('id', 'access', 'id = \'' . $uri->id . '\'', 0, 0, 0, 1) == '1') {
	if (isset($_POST['submit'])) {
		$form = $_POST['form'];

		if (empty($form['name']))
			$errors[] = $lang->t('common', 'name_to_short');
		if (!empty($form['name']) && $db->select('id', 'access', 'id != \'' . $uri->id . '\' AND name = \'' . $db->escape($form['name']) . '\'', 0, 0, 0, 1) == '1')
			$errors[] = $lang->t('access', 'access_level_already_exist');
		if (emptyCheck($form['modules']))
			$errors[] = $lang->t('access', 'select_modules');

		if (isset($errors)) {
			$tpl->assign('error_msg', comboBox($errors));
		} else {
			$update_values = array(
				'name' => $db->escape($form['name']),
				'modules' => buildAccessLevel($form['modules']),
			);

			$bool = $db->update('access', $update_values, 'id = \'' . $uri->id . '\'');

			$content = comboBox($bool ? $lang->t('access', 'edit_success') : $lang->t('access', 'edit_error'), uri('acp/access'));
		}
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$access = $db->select('name, modules', 'access', 'id = \'' . $uri->id . '\'');

		$tpl->assign('form', isset($form) ? $form : $access[0]);

		$mod_list = modules::modulesList();
		$mods_arr = explode(',', $access[0]['modules']);
		$c_mods_arr = count($mods_arr);

		foreach ($mod_list as $name => $info) {
			if ($info['dir'] == 'errors' || !$info['active']) {
				unset($mod_list[$name]);
			} else {
				for ($i = 0; $i < $c_mods_arr; ++$i) {
					if ($info['dir'] == substr($mods_arr[$i], 0, -2)) {
						$db_value = substr($mods_arr[$i], -1, 1);
						$mod_list[$name]['level_0_selected'] = selectAccessLevel($info['dir'], '0', $db_value);
						$mod_list[$name]['level_1_selected'] = selectAccessLevel($info['dir'], '1', $db_value);
						$mod_list[$name]['level_2_selected'] = selectAccessLevel($info['dir'], '2', $db_value);
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