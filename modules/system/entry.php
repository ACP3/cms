<?php
/**
 * System
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;
if (!$modules->check())
	redirect('errors/403');

switch ($modules->action) {
	case 'modactivation':
		if (isset($modules->gen['dir'])) {
			include 'modules/' . $modules->gen['dir'] . '/info.php';
			if (isset($mod_info['protected']) && $mod_info['protected']) {
				$text = lang('system', 'mod_deactivate_forbidden');
			} else {
				// TODO: Modulaktivierung

				$text = $bool ? lang('system', 'mod_activate_success') : lang('system', 'mod_activate_error');
			}
		} else {
			$text = lang('system', 'mod_activate_error');
		}
		$content = combo_box($text, uri('acp/system/mod_list'));
		break;
	case 'moddeactivation':
		if (isset($modules->gen['dir']) && $modules->is_active($modules->gen['dir'])) {
			include 'modules/' . $modules->gen['dir'] . '/info.php';
			if (isset($mod_info['protected']) && $mod_info['protected']) {
				$text = lang('system', 'mod_deactivate_forbidden');
			} else {
				// TODO: Moduldeaktivierung

				$text = $bool ? lang('system', 'mod_deactivate_success') : lang('system', 'mod_deactivate_error');
			}
		} else {
			$text = lang('system', 'mod_deactivate_error');
		}
		$content = combo_box($text, uri('acp/system/mod_list'));
		break;
	case 'configuration':
		$form = $_POST['form'];
		$i = 0;

		if (!ereg('[0-9]', $form['entries']))
			$errors[$i++] = lang('system', 'select_entries_per_page');
		if (!ereg('[0-9]', $form['flood']))
			$errors[$i++] = lang('system', 'type_in_flood_barrier');
		if (!ereg('[0-9]', $form['sef']))
			$errors[$i++] = lang('system', 'select_sef_uris');
		if (empty($form['date']))
			$errors[$i++] = lang('system', 'type_in_date_format');
		if (!ereg('[0-9]', $form['dst']))
			$errors[$i++] = lang('system', 'select_daylight_saving_time');
		if (!ereg('[0-9]', $form['time_zone']))
			$errors[$i++] = lang('system', 'select_time_zone');
		if (!ereg('[0-9]', $form['maintenance']))
			$errors[$i++] = lang('system', 'select_online_maintenance');
		if (strlen($form['maintenance_msg']) < 3)
			$errors[$i++] = lang('system', 'maintenance_message_to_short');
		if (empty($form['title']))
			$errors[$i++] = lang('system', 'title_to_short');
		if (empty($form['db_host']))
			$errors[$i++] = lang('system', 'type_in_db_host');
		if (empty($form['db_user']))
			$errors[$i++] = lang('system', 'type_in_db_username');
		if (empty($form['db_name']))
			$errors[$i++] = lang('system', 'type_in_db_name');
		if (empty($form['db_type']))
			$errors[$i++] = lang('system', 'select_db_type');

		if (isset($errors)) {
			$error_msg = combo_box($errors);
		} else {
			$bool = $config->general($form);

			$content = combo_box($bool ? lang('system', 'config_edit_success') : lang('system', 'config_edit_error'), uri('acp/system/configuration'));
		}
		break;
	case 'lang':
		$dir = isset($modules->gen['dir']) && is_file('languages/' . $modules->gen['dir'] . '/info.php') ? $modules->gen['dir'] : 0;

		$bool = $config->general(array('lang' => $dir));
		$text = $bool && !empty($dir) ? lang('system', 'lang_edit_success') : lang('system', 'lang_edit_error');

		$content = combo_box($text, uri('acp/system/lang_design'));
		break;
	case 'design';
		$dir = isset($modules->gen['dir']) && is_file('designs/' . $modules->gen['dir'] . '/info.php') ? $modules->gen['dir'] : 0;

		$bool = $config->general(array('design' => $dir));
		$text = $bool && !empty($dir) ? lang('system', 'design_edit_success') : lang('system', 'design_edit_error');

		$content = combo_box($text, uri('acp/system/lang_design'));
		break;
	case 'sql':
		$update_text = str_replace(array("\r\n", "\r", "\n"), "\n", $_POST['update_text']);
		$update_file['name'] = isset($_FILES['update_file']['name']) ? $_FILES['update_file']['name'] : '';
		$update_file['size'] = isset($_FILES['update_file']['size']) ? $_FILES['update_file']['size'] : 0;
		$i = 0;

		if (empty($update_text) && empty($update_file['name']) && empty($update_file['size']))
			$errors[$i++] = lang('system', 'type_in_sql_text_or_upload_file');
		if (!empty($update_file['name']) && $update_file['size'] > 0 && !eregi('.+(\.sql)$', $update_file['name']))
			$errors[$i++] = lang('system', 'wrong_file_format');

		if (isset($errors)) {
			$error_msg = combo_box($errors);
		} else {
			$updates = !empty($update_file['name']) ? file($_FILES['update_file']['tmp_name']) : explode("\n", $update_text);
			$i = 0;
			foreach ($updates as $row) {
				if (!empty($row)) {
					$row = str_replace('{pre}', CONFIG_DB_PRE, $row);

					$bool = $db->query($row, 3);
					$results[$i]['sql_statement'] = $row;
					$results[$i]['bool'] = $bool ? '1' : '0';
					$i++;
				}
			}
		}
		break;
	default:
		redirect('errors/404');
}
?>