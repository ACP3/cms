<?php
/**
 * Comments
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3') && !defined('IN_ADM'))
	exit;
if (!$modules->check('comments', 'entry'))
	redirect('errors/403');

switch ($modules->action) {
	case 'create':
		if (isset($_POST['module']) && isset($_POST['entry_id']) && ereg('[0-9]', $_POST['entry_id'])) {
			$ip = $_SERVER['REMOTE_ADDR'];
			$form = $_POST['form'];
			$module = $_POST['module'];
			$entry_id = $_POST['entry_id'];
			$i = 0;

			//Flood Sperre
			$flood = $db->select('date', 'comments', 'ip = \'' . $ip . '\'', 'id DESC', '1');
			$flood_time = $flood[0]['date'] + CONFIG_FLOOD;
			$time = date_aligned(2, time());

			if ($flood_time > $time)
				$errors[$i++] = sprintf(lang('common', 'flood_no_entry_possible'), $flood_time - $time);
			if (empty($form['name']))
				$errors[$i++] = lang('common', 'name_to_short');
			if (strlen($form['message']) < 3)
				$errors[$i++] = lang('common', 'message_to_short');
			if (!$modules->is_active($db->escape($module, 2)))
				$errors[$i++] = lang('comments', 'module_doesnt_exist');

			if (isset($errors)) {
				$error_msg = combo_box($errors);
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
	case 'edit':
		$form = $_POST['form'];
		$i = 0;

		if (empty($form['name']))
			$errors[$i++] = lang('common', 'name_to_short');
		if (strlen($form['message']) < 3)
			$errors[$i++] = lang('common', 'message_to_short');

		if (isset($errors)) {
			$error_msg = combo_box($errors);
		} else {
			$update_values = array(
				'name' => $db->escape($form['name']),
				'message' => $db->escape($form['message']),
			);

			$bool = $db->update('comments', $update_values, 'id = \'' . $modules->id . '\'');

			$content = combo_box($bool ? lang('comments', 'edit_success') : lang('comments', 'edit_error'), uri('acp/comments'));
		}
		break;
	case 'delete_com_by_mod':
		if (isset($_POST['entries']) && is_array($_POST['entries']))
			$entries = $_POST['entries'];
		elseif (isset($modules->gen['entries']) && eregi('^([a-z0-9_\-|]+)$', $modules->gen['entries']))
			$entries = $modules->gen['entries'];

		if (is_array($entries)) {
			$marked_entries = '';
			foreach ($entries as $entry) {
				$marked_entries.= $entry . '|';
			}
			$content = combo_box(lang('comments', 'confirm_delete'), uri('acp/comments/adm_list/action_delete_com_by_mod/entries_' . $marked_entries), uri('acp/comments'));
		} elseif (ereg('^([a-z0-9_\-|]+)$', $entries) && isset($modules->gen['confirmed'])) {
			$marked_entries = explode('|', $entries);
			$bool = 0;
			foreach ($marked_entries as $entry) {
				if (!empty($entry) && ereg('[a-z0-9]', $entry) && $db->select('id', 'comments', 'module = \'' . $entry . '\'', 0, 0, 0, 1) > '0')
					$bool = $db->delete('comments', 'module = \'' . $entry . '\'');
			}
			$content = combo_box($bool ? lang('comments', 'delete_success') : lang('comments', 'delete_error'), uri('acp/comments'));
		} else {
			redirect('errors/404');
		}
		break;
	case 'delete_comments':
		if (isset($_POST['entries']) && is_array($_POST['entries']))
			$entries = $_POST['entries'];
		elseif (isset($modules->gen['entries']) && ereg('^([0-9|]+)$', $modules->gen['entries']))
			$entries = $modules->gen['entries'];

		if (is_array($entries)) {
			$marked_entries = '';
			foreach ($entries as $entry) {
				$marked_entries.= $entry . '|';
			}
			$content = combo_box(lang('comments', 'confirm_delete'), uri('acp/comments/adm_list/action_delete_comments/entries_' . $marked_entries), uri('acp/comments'));
		} elseif (ereg('^([0-9|]+)$', $entries) && isset($modules->gen['confirmed'])) {
			$marked_entries = explode('|', $entries);
			$bool = 0;
			foreach ($marked_entries as $entry) {
				if (!empty($entry) && ereg('[0-9]', $entry) && $db->select('id', 'comments', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1')
					$bool = $db->delete('comments', 'id = \'' . $entry . '\'');
			}
			$content = combo_box($bool ? lang('comments', 'delete_success') : lang('comments', 'delete_error'), uri('acp/comments'));
		} else {
			redirect('errors/404');
		}
		break;
	default:
		redirect('errors/404');
}
?>