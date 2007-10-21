<?php
/**
 * Emoticons
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;
if (!$modules->check('emoticons', 'entry'))
	redirect('errors/403');

switch ($modules->action) {
	case 'create':
		$form = $_POST['form'];
		if (!empty($_FILES['picture']['tmp_name'])) {
			$file['tmp_name'] = $_FILES['picture']['tmp_name'];
			$file['name'] = $_FILES['picture']['name'];
			$file['size'] = $_FILES['picture']['size'];
		}

		if (empty($form['code']))
			$errors[] = lang('emoticons', 'type_in_code');
		if (empty($form['description']))
			$errors[] = lang('emoticons', 'type_in_description');
		if (!isset($file) || empty($file['size']) || !$validate->is_picture($file['tmp_name']))
			$errors[] = lang('emoticons', 'select_picture');

		if (isset($errors)) {
			combo_box($errors);
		} else {
			$result = move_file($file['tmp_name'], $file['name'], 'emoticons');

			$insert_values = array(
				'id' => '',
				'code' => $db->escape($form['code']),
				'description' => $db->escape($form['description']),
				'img' => $result['name'],
			);

			$bool = $db->insert('emoticons', $insert_values);

			$cache->create('emoticons', $db->select('code, description, img', 'emoticons'));

			$content = combo_box($bool ? lang('emoticons', 'create_success') : lang('emoticons', 'create_error'), uri('acp/emoticons'));
		}
		break;
	case 'edit':
		$form = $_POST['form'];
		if (!empty($_FILES['picture']['tmp_name'])) {
			$file['tmp_name'] = $_FILES['picture']['tmp_name'];
			$file['name'] = $_FILES['picture']['name'];
			$file['size'] = $_FILES['picture']['size'];
		}

		if (empty($form['code']))
			$errors[] = lang('emoticons', 'type_in_code');
		if (empty($form['description']))
			$errors[] = lang('emoticons', 'type_in_description');
		if (isset($file) && (empty($file['size']) || !$validate->is_picture($file['tmp_name'])))
			$errors[] = lang('emoticons', 'select_picture');

		if (isset($errors)) {
			combo_box($errors);
		} else {
			$new_file_sql = null;
			if (isset($file)) {
				$result = move_file($file['tmp_name'], $file['name'], 'emoticons');
				$new_file_sql = array('img' => $result['name'],);
			}

			$update_values = array(
				'code' => $db->escape($form['code']),
				'description' => $db->escape($form['description']),
			);
			if (is_array($new_file_sql)) {
				$update_values = array_merge($update_values, $new_file_sql);
			}

			$bool = $db->update('emoticons', $update_values, 'id = \'' . $modules->id . '\'');

			$cache->create('emoticons', $db->select('code, description, img', 'emoticons'));

			$content = combo_box($bool ? lang('emoticons', 'edit_success') : lang('emoticons', 'edit_error'), uri('acp/emoticons'));
		}
		break;
	case 'delete':
		if (isset($_POST['entries']) && is_array($_POST['entries']))
			$entries = $_POST['entries'];
		elseif (isset($modules->gen['entries']) && preg_match('/^([\d|]+)$/', $modules->gen['entries']))
			$entries = $modules->gen['entries'];

		if (is_array($entries)) {
			$marked_entries = '';
			foreach ($entries as $entry) {
				$marked_entries.= $entry . '|';
			}
			$content = combo_box(lang('emoticons', 'confirm_delete'), uri('acp/emoticons/adm_list/action_delete/entries_' . $marked_entries), uri('acp/emoticons'));
		} elseif (preg_match('/^([\d|]+)$/', $entries) && isset($modules->gen['confirmed'])) {
			$marked_entries = explode('|', $entries);
			$bool = 0;
			foreach ($marked_entries as $entry) {
				if (!empty($entry) && $validate->is_number($entry) && $db->select('id', 'emoticons', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1')
					// Datei ebenfalls löschen
					$file = $db->select('img', 'emoticons', 'id = \'' . $entry . '\'');
					if (is_file('uploads/emoticons/' . $file[0]['img'])) {
						unlink('uploads/emoticons/' . $file[0]['img']);
					}
					$bool = $db->delete('emoticons', 'id = \'' . $entry . '\'');
			}
			$cache->create('emoticons', $db->select('code, description, img', 'emoticons'));

			$content = combo_box($bool ? lang('emoticons', 'delete_success') : lang('emoticons', 'delete_error'), uri('acp/emoticons'));
		} else {
			redirect('errors/404');
		}
		break;
	default:
		redirect('errors/404');
}
?>