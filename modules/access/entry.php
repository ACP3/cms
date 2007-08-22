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
if (!$modules->check(0, 'entry'))
	redirect('errors/403');

switch ($modules->action) {
	case 'create':
		$form = $_POST['form'];

		if (empty($form['name']))
			$errors[] = lang('common', 'name_to_short');
		if (!empty($form['name']) && $db->select('id', 'access', 'name = \'' . $db->escape($form['name']) . '\'', 0, 0, 0, 1) == '1')
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
			$error_msg = combo_box($errors);
		} else {
			// String für die einzelnen Zugriffslevel auf die Module erstellen
			$form['modules']['errors'] = '2';
			ksort($form['modules']);
			$insert_mods = '';

			foreach ($form['modules'] as $module => $level) {
				$insert_mods.= $module . ':' . $level . ',';
			}

			$insert_values = array(
				'id' => '',
				'name' => $db->escape($form['name']),
				'modules' => substr($insert_mods, 0, -1),
			);

			$bool = $db->insert('access', $insert_values);

			$content = combo_box($bool ? lang('access', 'create_success') : lang('access', 'create_error'), uri('acp/access'));
		}
		break;
	case 'edit':
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
			$error_msg = combo_box($errors);
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
		break;
	case 'delete':
		if (isset($_POST['entries']) && is_array($_POST['entries']))
			$entries = $_POST['entries'];
		elseif (isset($modules->gen['entries']) && ereg('^([0-9|]+)$', $modules->gen['entries']))
			$entries = $modules->gen['entries'];

		if (is_array($entries)) {
			$marked_entries = '';
			foreach ($entries as $entry) {
				$marked_entries.= $entry . '|';
			}
			$content = combo_box(lang('access', 'confirm_delete'), uri('acp/access/adm_list/action_delete/entries_' . $marked_entries), uri('acp/access'));
		} elseif (ereg('^([0-9|]+)$', $entries) && isset($modules->gen['confirmed'])) {
			$marked_entries = explode('|', $entries);
			$bool = 0;
			$level_undeletable = 0;

			foreach ($marked_entries as $entry) {
				if (!empty($entry) && ereg('[0-9]', $entry) && $db->select('id', 'access', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1') {
					if ($entry == '1' || $entry == '2') {
						$level_undeletable = 1;
					} else {
						$bool = $db->delete('access', 'id = \'' . $entry . '\'');
					}
				}
			}
			if ($level_undeletable) {
				$text = lang('access', 'access_level_undeletable');
			} else {
				$text = $bool ? lang('access', 'delete_success') : lang('access', 'delete_error');
			}
			$content = combo_box($text, uri('acp/access'));
		} else {
			redirect('errors/404');
		}
		break;
	default:
		redirect('errors/404');
}
?>