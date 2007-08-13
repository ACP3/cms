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
switch ($modules->action) {
	case 'create':
		$form = $_POST['form'];
		$i = 0;

		if (empty($form['name']))
			$errors[$i++] = lang('common', 'name_to_short');
		if (!empty($form['name']) && $db->select('id', 'access', 'name = \'' . $db->escape($form['name']) . '\'', 0, 0, 0, 1) == '1')
			$errors[$i++] = lang('access', 'access_level_already_exist');
		if (!isset($form['modules']) || !is_array($form['modules']))
			$errors[$i++] = lang('access', 'select_modules');
		if (isset($form['modules']) && is_array($form['modules']) && !in_array('home', $form['modules']))
			$errors[$i++] = lang('access', 'select_home');

		if (isset($errors)) {
			$error_msg = combo_box($errors);
		} else {
			$insert_mods = '';
			foreach ($form['modules'] as $mod) {
				$insert_mods.= $mod . '|';
			}
			$insert_values = array(
				'id' => '',
				'name' => $db->escape($form['name']),
				'mods' => $insert_mods,
			);

			$bool = $db->insert('access', $insert_values);

			$content = combo_box($bool ? lang('access', 'create_success') : lang('access', 'create_error'), uri('acp/access'));
		}
		break;
	case 'edit':
		$form = $_POST['form'];
		$i = 0;

		if (empty($form['name']))
			$errors[$i++] = lang('common', 'name_to_short');
		if (!empty($form['name']) && $db->select('id', 'access', 'id != \'' . $modules->id . '\' AND name = \'' . $db->escape($form['name']) . '\'', 0, 0, 0, 1) == '1')
			$errors[$i++] = lang('access', 'access_level_already_exist');
		if (!isset($form['modules']) || !is_array($form['modules']))
			$errors[$i++] = lang('access', 'select_modules');
		if (isset($form['modules']) && is_array($form['modules']) && !in_array('home', $form['modules']))
			$errors[$i++] = lang('access', 'select_home');

		if (isset($errors)) {
			$error_msg = combo_box($errors);
		} else {
			$insert_mods = '';
			foreach ($form['modules'] as $mod) {
				$insert_mods.= $mod . '|';
			}
			$update_values = array(
				'name' => $db->escape($form['name']),
				'mods' => $insert_mods,
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
			foreach ($marked_entries as $entry) {
				if (!empty($entry) && ereg('[0-9]', $entry) && $db->select('id', 'access', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1') {
					if ($entry == '1') {
						$admin_access = 1;
						break;
					} else {
						$bool = $db->delete('access', 'id = \'' . $entry . '\'');
					}
				}
			}
			if ($bool) {
				$text = lang('access', 'delete_success');
			} elseif ($admin_access) {
				$text = lang('access', 'admin_access_undeletable');
			} else {
				$text = lang('access', 'delete_error');
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