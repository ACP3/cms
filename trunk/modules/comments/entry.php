<?php
/**
 * Comments
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_FRONTEND') && !defined('IN_ACP'))
	exit;
if (!$modules->check('comments', 'entry'))
	redirect('errors/403');

switch ($modules->action) {
	case 'create':
		if (isset($_POST['module']) && isset($_POST['entry_id']) && $validate->is_number($_POST['entry_id'])) {
			$ip = $_SERVER['REMOTE_ADDR'];
			$form = $_POST['form'];
			$module = $_POST['module'];
			$entry_id = $_POST['entry_id'];

			// Flood Sperre
			$flood = $db->select('date', 'comments', 'ip = \'' . $ip . '\'', 'id DESC', '1');
			if (count($flood) == '1') {
				$flood_time = $flood[0]['date'] + CONFIG_FLOOD;
			}
			$time = date_aligned(2, time());

			if (isset($flood_time) && $flood_time > $time)
				$errors[] = sprintf(lang('common', 'flood_no_entry_possible'), $flood_time - $time);
			if (empty($form['name']))
				$errors[] = lang('common', 'name_to_short');
			if (strlen($form['message']) < 3)
				$errors[] = lang('common', 'message_to_short');
			if (!$modules->check($db->escape($module, 2), 'list', 'frontend'))
				$errors[] = lang('comments', 'module_doesnt_exist');

			if (isset($errors)) {
				combo_box($errors);
			} else {
				$insert_values = array(
					'id' => '',
					'ip' => $ip,
					'date' => $time,
					'name' => $db->escape($form['name']),
					'message' => $db->escape($form['message']),
					'module' => $db->escape($module, 2),
					'entry_id' => $entry_id,
				);

				$bool = $db->insert('comments', $insert_values);

				$content = combo_box($bool ? lang('comments', 'create_success') : lang('comments', 'create_error'), uri($module . '/details/id_' . $entry_id));
			}
		} else {
			$content = combo_box(lang('common', 'entry_not_found'), ROOT_DIR);
		}
		break;
	case 'acp_edit':
		$form = $_POST['form'];

		if (empty($form['name']))
			$errors[] = lang('common', 'name_to_short');
		if (strlen($form['message']) < 3)
			$errors[] = lang('common', 'message_to_short');

		if (isset($errors)) {
			combo_box($errors);
		} else {
			$update_values = array(
				'name' => $db->escape($form['name']),
				'message' => $db->escape($form['message']),
			);

			$bool = $db->update('comments', $update_values, 'id = \'' . $modules->id . '\'');

			$content = combo_box($bool ? lang('comments', 'edit_success') : lang('comments', 'edit_error'), uri('comments/acp_list'));
		}
		break;
	case 'acp_delete_com_by_mod':
		if (isset($_POST['entries']) && is_array($_POST['entries']))
			$entries = $_POST['entries'];
		elseif (isset($modules->gen['entries']) && preg_match('/^([\w|]+)$/', $modules->gen['entries']))
			$entries = $modules->gen['entries'];

		if (is_array($entries)) {
			$marked_entries = '';
			foreach ($entries as $entry) {
				$marked_entries.= $entry . '|';
			}
			$content = combo_box(lang('comments', 'confirm_delete'), uri('comments/acp_list/action_delete_com_by_mod/entries_' . $marked_entries), uri('comments/acp_list'));
		} elseif (preg_match('/^([\w|]+)$/', $entries) && isset($modules->gen['confirmed'])) {
			$marked_entries = explode('|', $entries);
			$bool = 0;
			foreach ($marked_entries as $entry) {
				if (!empty($entry) && preg_match('/^(\w+)$/', $entry) && $db->select('id', 'comments', 'module = \'' . $entry . '\'', 0, 0, 0, 1) > '0')
					$bool = $db->delete('comments', 'module = \'' . $entry . '\'');
			}
			$content = combo_box($bool ? lang('comments', 'delete_success') : lang('comments', 'delete_error'), uri('comments/acp_list'));
		} else {
			redirect('errors/404');
		}
		break;
	case 'acp_delete_comments':
		if (isset($_POST['entries']) && is_array($_POST['entries']))
			$entries = $_POST['entries'];
		elseif (isset($modules->gen['entries']) && preg_match('/^([\d|]+)$/', $modules->gen['entries']))
			$entries = $modules->gen['entries'];

		if (is_array($entries)) {
			$marked_entries = '';
			foreach ($entries as $entry) {
				$marked_entries.= $entry . '|';
			}
			$content = combo_box(lang('comments', 'confirm_delete'), uri('comments/acp_list/action_delete_comments/entries_' . $marked_entries), uri('comments/acp_list'));
		} elseif (preg_match('/^([\d|]+)$/', $entries) && isset($modules->gen['confirmed'])) {
			$marked_entries = explode('|', $entries);
			$bool = 0;
			foreach ($marked_entries as $entry) {
				if (!empty($entry) && $validate->is_number($entry) && $db->select('id', 'comments', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1')
					$bool = $db->delete('comments', 'id = \'' . $entry . '\'');
			}
			$content = combo_box($bool ? lang('comments', 'delete_success') : lang('comments', 'delete_error'), uri('comments/acp_list'));
		} else {
			redirect('errors/404');
		}
		break;
	default:
		redirect('errors/404');
}
?>