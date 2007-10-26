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
if (!$modules->check('system', 'entry'))
	redirect('errors/403');

switch ($modules->action) {
	case 'configuration':
		$form = $_POST['form'];

		if (!$validate->is_number($form['entries']))
			$errors[] = lang('system', 'select_entries_per_page');
		if (!$validate->is_number($form['flood']))
			$errors[] = lang('system', 'type_in_flood_barrier');
		if (!$validate->is_number($form['sef']))
			$errors[] = lang('system', 'select_sef_uris');
		if (empty($form['date']))
			$errors[] = lang('system', 'type_in_date_format');
		if (!is_numeric($form['time_zone']))
			$errors[] = lang('common', 'select_time_zone');
		if (!$validate->is_number($form['dst']))
			$errors[] = lang('common', 'select_daylight_saving_time');
		if (!$validate->is_number($form['maintenance']))
			$errors[] = lang('system', 'select_online_maintenance');
		if (strlen($form['maintenance_msg']) < 3)
			$errors[] = lang('system', 'maintenance_message_to_short');
		if (empty($form['title']))
			$errors[] = lang('system', 'title_to_short');
		if (empty($form['db_host']))
			$errors[] = lang('system', 'type_in_db_host');
		if (empty($form['db_user']))
			$errors[] = lang('system', 'type_in_db_username');
		if (empty($form['db_name']))
			$errors[] = lang('system', 'type_in_db_name');
		if (empty($form['db_type']))
			$errors[] = lang('system', 'select_db_type');

		if (isset($errors)) {
			combo_box($errors);
		} else {
			$bool = $config->general($form);

			$content = combo_box($bool ? lang('system', 'config_edit_success') : lang('system', 'config_edit_error'), uri('acp/system/configuration'));
		}
		break;
	case 'designs';
		$dir = isset($modules->gen['dir']) && is_file('designs/' . $modules->gen['dir'] . '/info.php') ? $modules->gen['dir'] : 0;
		$bool = false;

		if (!empty($dir)) {
			$bool = $config->general(array('design' => $dir));
		}
		$text = $bool ? lang('system', 'designs_edit_success') : lang('system', 'designs_edit_error');

		// Cache leeren und diverse Parameter für die Template Engine abändern
		$cache->purge();
		$tpl->template_dir = './designs/' . $dir . '/';
		$tpl->assign('design_path', ROOT_DIR . 'designs/' . $dir . '/');

		$content = combo_box($text, uri('acp/system/designs'));
		break;
	case 'languages':
		$dir = isset($modules->gen['dir']) && is_file('languages/' . $modules->gen['dir'] . '/info.php') ? $modules->gen['dir'] : 0;
		$bool = false;

		if (!empty($dir)) {
			$bool = $config->general(array('lang' => $dir));
		}
		$text = $bool ? lang('system', 'languages_edit_success') : lang('system', 'languages_edit_error');

		$content = combo_box($text, uri('acp/system/languages'));
		break;
	case 'modactivation':
		if (isset($modules->gen['dir']) && is_file('modules/' . $modules->gen['dir'] . '/module.xml')) {
			$info = $modules->parseInfo($modules->gen['dir']);
			if ($info['protected']) {
				$text = lang('system', 'mod_deactivate_forbidden');
			} else {
				$path = 'modules/' . $modules->gen['dir'] . '/module.xml';

				$xml = simplexml_load_file($path);
				$xml->info->active = '1';
				$bool = $xml->asXML($path);

				$text = $bool ? lang('system', 'mod_activate_success') : lang('system', 'mod_activate_error');
			}
		} else {
			$text = lang('system', 'mod_activate_error');
		}
		$content = combo_box($text, uri('acp/system/mod_list'));
		break;
	case 'moddeactivation':
		if (isset($modules->gen['dir']) && is_file('modules/' . $modules->gen['dir'] . '/module.xml')) {
			$info = $modules->parseInfo($modules->gen['dir']);
			if ($info['protected']) {
				$text = lang('system', 'mod_deactivate_forbidden');
			} else {
				$path = 'modules/' . $modules->gen['dir'] . '/module.xml';

				$xml = simplexml_load_file($path);
				$xml->info->active = '0';
				$bool = $xml->asXML($path);

				$text = $bool ? lang('system', 'mod_deactivate_success') : lang('system', 'mod_deactivate_error');
			}
		} else {
			$text = lang('system', 'mod_deactivate_error');
		}
		$content = combo_box($text, uri('acp/system/mod_list'));
		break;
	case 'sql_export':
		$form = $_POST['form'];

		if (empty($form['tables']))
			$errors[] = lang('system', 'select_sql_tables');
		if ($form['output'] != 'file' && $form['output'] != 'text')
			$errors[] = lang('system', 'select_output');
		if ($form['export_type'] != 'complete' && $form['export_type'] != 'structure' && $form['export_type'] != 'data')
			$errors[] = lang('system', 'select_export_type');

		if (isset($errors)) {
			combo_box($errors);
		} else {
			$structure = '';
			$data = '';
			foreach ($form['tables'] as $table) {
				if ($form['export_type'] == 'complete' || $form['export_type'] == 'structure') {
					$result = $db->query('SHOW CREATE TABLE ' . $table);
					if (is_array($result)) {
						$structure.= '-- ' . sprintf(lang('system', 'structure_of_table'), $table) . "\n\n";
						$structure.= $result[0]['Create Table'] . ';' . "\n\n";
					}
				}
				if ($form['export_type'] == 'complete' || $form['export_type'] == 'data') {
					$resultsets = $db->select('*', substr($table, strlen(CONFIG_DB_PRE), strlen($table)));
					if (count($resultsets) > 0) {
						$data.= "\n" . '-- '. sprintf(lang('system', 'data_of_table'), $table) . "\n\n";
						$fields = '';
						foreach ($resultsets[0] as $field => $content) {
							$fields.= $field . ', ';
						}
						foreach ($resultsets as $row) {
							$values = '';
							foreach ($row as $value) {
								$values.= '\'' . $value . '\', ';
							}
							$data.= 'INSERT INTO ' . $table . ' (' . substr($fields, 0, -2) . ') VALUES (' . substr($values, 0, -2) . ');' . "\n";
						}
					}
				}
			}
			$export = $structure . $data;
			if ($form['output'] == 'file') {
				ob_end_clean();
				define('CUSTOM_CONTENT_TYPE', 'text/sql');
				header('Content-Disposition: attachment; filename=' . CONFIG_DB_NAME . '_export.sql');
				die($export);
			} else {
				$tpl->assign('export', htmlentities($export, ENT_QUOTES, 'UTF-8'));
			}
		}
		break;
	default:
		redirect('errors/404');
}
?>