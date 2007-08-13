<?php
/**
 * Guestbook
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3') && !defined('IN_ADM'))
	exit;

switch ($modules->action) {
	case 'create':
		$ip = $_SERVER['REMOTE_ADDR'];
		$form = $_POST['form'];
		$i = 0;

		// Flood Sperre
		$flood = $db->select('date', 'gb', 'ip = \'' . $ip . '\'', 'id DESC', '1');
		$flood_time = $flood[0]['date'] + CONFIG_FLOOD;
		$time = date_aligned(2, time());

		if ($flood_time > $time)
			$errors[$i++] = sprintf(lang('common', 'flood_no_entry_possible'), $flood_time - $time);
		if (empty($form['name']))
			$errors[$i++] = lang('common', 'name_to_short');
		if (!empty($form['mail']) && !$validate->email($form['mail']))
			$errors[$i++] = lang('common', 'wrong_email_format');
		if (strlen($form['message']) < 3)
			$errors[$i++] = lang('common', 'message_to_short');

		if (isset($errors)) {
			$error_msg = combo_box($errors);
		} else {
			$insert_values = array(
				'id' => '',
				'ip' => $ip,
				'date' => $time,
				'name' => $db->escape($form['name']),
				'message' => $db->escape($form['message']),
				'website' => $db->escape($form['website'], 2),
				'mail' => $form['mail'],
			);

			$bool = $db->insert('gb', $insert_values);

			$content = combo_box($bool ? lang('gb', 'create_success') : lang('gb', 'create_error'), uri('gb'));
		}
		break;
	case 'edit':
		if (!$modules->check())
			redirect('errors/403');
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

			$bool = $db->update('gb', $update_values, 'id = \'' . $modules->id . '\'');

			$content = combo_box($bool ? lang('gb', 'edit_success') : lang('gb', 'edit_error'), uri('acp/gb'));
		}
		break;
	case 'delete':
		if (!$modules->check())
			redirect('errors/403');
		if (isset($_POST['entries']) && is_array($_POST['entries']))
			$entries = $_POST['entries'];
		elseif (isset($modules->gen['entries']) && ereg('^([0-9|]+)$', $modules->gen['entries']))
			$entries = $modules->gen['entries'];

		if (is_array($entries)) {
			$marked_entries = '';
			foreach ($entries as $entry) {
				$marked_entries.= $entry . '|';
			}
			$content = combo_box(lang('gb', 'confirm_delete'), uri('acp/gb/adm_list/action_delete/entries_' . $marked_entries), uri('acp/gb'));
		} elseif (ereg('^([0-9|]+)$', $entries) && isset($modules->gen['confirmed'])) {
			$marked_entries = explode('|', $entries);
			$bool = 0;
			foreach ($marked_entries as $entry) {
				if (!empty($entry) && ereg('[0-9]', $entry) && $db->select('id', 'gb', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1')
					$bool = $db->delete('gb', 'id = \'' . $entry . '\'');
			}
			$content = combo_box($bool ? lang('gb', 'delete_success') : lang('gb', 'delete_error'), uri('acp/gb'));
		} else {
			redirect('errors/404');
		}
		break;
	default:
		redirect('errors/404');
}
?>