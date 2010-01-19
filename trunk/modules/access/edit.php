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

require_once ACP3_ROOT . 'modules/access/functions.php';

if (validate::isNumber($uri->id) && $db->countRows('*', 'access', 'id = \'' . $uri->id . '\'') == '1') {
	if (isset($_POST['form'])) {
		$form = $_POST['form'];

		if (empty($form['name']))
			$errors[] = $lang->t('common', 'name_to_short');
		if (!empty($form['name']) && $db->countRows('*', 'access', 'id != \'' . $uri->id . '\' AND name = \'' . db::escape($form['name']) . '\'') == '1')
			$errors[] = $lang->t('access', 'access_level_already_exists');

		if (isset($errors)) {
			$tpl->assign('error_msg', comboBox($errors));
		} else {
			$update_values = array(
				'name' => db::escape($form['name']),
				'modules' => buildAccessLevel($form['modules']),
			);

			$bool = $db->update('access', $update_values, 'id = \'' . $uri->id . '\'');

			$content = comboBox($bool !== null ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), uri('acp/access'));
		}
	}
	if (!isset($_POST['form']) || isset($errors) && is_array($errors)) {
		$access = $db->select('name, modules', 'access', 'id = \'' . $uri->id . '\'');

		$tpl->assign('form', isset($form) ? $form : $access[0]);

		$mod_list = modules::modulesList();
		$mods_arr = explode(',', $access[0]['modules']);
		$c_mods_arr = count($mods_arr);

		foreach ($mod_list as $name => $info) {
			if ($info['dir'] == 'errors' || !$info['active']) {
				unset($mod_list[$name]);
			} else {
				$dir = $info['dir'];
				if (isset($form['modules'])) {
					$mod_list[$name]['read_checked'] = isset($form['modules'][$dir]['read']) ? ' checked="checked"' : '';
					$mod_list[$name]['create_checked'] = isset($form['modules'][$dir]['create']) ? ' checked="checked"' : '';
					$mod_list[$name]['edit_checked'] = isset($form['modules'][$dir]['edit']) ? ' checked="checked"' : '';
					$mod_list[$name]['delete_checked'] = isset($form['modules'][$dir]['delete']) ? ' checked="checked"' : '';
				} else {
					$db_value = '';
					for ($i = 0; $i < $c_mods_arr; ++$i) {
						$pos = strrpos($mods_arr[$i], ':');
						if ($info['dir'] == substr($mods_arr[$i], 0, $pos)) {
							$db_value = substr($mods_arr[$i], $pos + 1);
							break;
						}
					}

					$mod_list[$name]['read_checked'] = '';
					$mod_list[$name]['create_checked'] = '';
					$mod_list[$name]['edit_checked'] = '';
					$mod_list[$name]['delete_checked'] = '';

					if ($db_value - 8 >= 0) {
						$mod_list[$name]['delete_checked'] = ' checked="checked"';
						$db_value-= 8;
					}
					if ($db_value - 4 >= 0) {
						$mod_list[$name]['edit_checked'] = ' checked="checked"';
						$db_value-= 4;
					}
					if ($db_value - 2 >= 0) {
						$mod_list[$name]['create_checked'] = ' checked="checked"';
						$db_value-= 2;
					}
					if ($db_value - 1 >= 0) {
						$mod_list[$name]['read_checked'] = ' checked="checked"';
						$db_value-= 1;
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
