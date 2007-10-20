<?php
/**
 * Gallery
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP'))
	exit;
if (!$modules->check('gallery', 'entry'))
	redirect('errors/403');

switch ($modules->action) {
	case 'acp_create':
		$form = $_POST['form'];

		if (!$validate->date($form))
			$errors[] = lang('common', 'select_date');
		if (strlen($form['name']) < 3)
			$errors[] = lang('gallery', 'type_in_gallery_name');

		if (isset($errors)) {
			combo_box($errors);
		} else {
			$start_date = date_aligned(3, array($form['start_hour'], $form['start_min'], 0, $form['start_month'], $form['start_day'], $form['start_year']));
			$end_date = date_aligned(3, array($form['end_hour'], $form['end_min'], 0, $form['end_month'], $form['end_day'], $form['end_year']));

			$insert_values = array(
				'id' => '',
				'start' => $start_date,
				'end' => $end_date,
				'name' => $db->escape($form['name']),
			);

			$bool = $db->insert('gallery', $insert_values);

			$content = combo_box($bool ? lang('gallery', 'create_success') : lang('gallery', 'create_error'), uri('gallery/acp_list'));
		}
		break;
	case 'acp_edit_gallery':
		$form = $_POST['form'];

		if (!$validate->date($form))
			$errors[] = lang('common', 'select_date');
		if (strlen($form['name']) < 3)
			$errors[] = lang('gallery', 'type_in_gallery_name');

		if (isset($errors)) {
			combo_box($errors);
		} else {
			$start_date = date_aligned(3, array($form['start_hour'], $form['start_min'], 0, $form['start_month'], $form['start_day'], $form['start_year']));
			$end_date = date_aligned(3, array($form['end_hour'], $form['end_min'], 0, $form['end_month'], $form['end_day'], $form['end_year']));

			$update_values = array(
				'start' => $start_date,
				'end' => $end_date,
				'name' => $db->escape($form['name']),
			);

			$bool = $db->update('gallery', $update_values, 'id = \'' . $modules->id . '\'');

			$cache->create('gallery_pics_id_' . $modules->id, $db->query('SELECT g.name, p.id FROM ' . CONFIG_DB_PRE . 'gallery g LEFT JOIN ' . CONFIG_DB_PRE . 'galpics p ON g.id = \'' . $modules->id . '\' AND p.gallery_id = \'' . $modules->id . '\' ORDER BY p.pic ASC, p.id ASC'));

			$content = combo_box($bool ? lang('gallery', 'edit_success') : lang('gallery', 'edit_error'), uri('gallery/acp_list'));
		}
		break;
	case 'acp_delete_gallery':
		if (isset($_POST['entries']) && is_array($_POST['entries']))
			$entries = $_POST['entries'];
		elseif (isset($modules->gen['entries']) && preg_match('/^([\d|]+)$/', $modules->gen['entries']))
			$entries = $modules->gen['entries'];

		if (is_array($entries)) {
			$marked_entries = '';
			foreach ($entries as $entry) {
				$marked_entries.= $entry . '|';
			}
			$content = combo_box(lang('gallery', 'confirm_delete'), uri('gallery/acp_list/action_delete_gallery/entries_' . $marked_entries), uri('gallery/acp_list'));
		} elseif (preg_match('/^([\d|]+)$/', $entries) && isset($modules->gen['confirmed'])) {
			$marked_entries = explode('|', $entries);
			$bool = 0;
			$bool2 = 0;
			foreach ($marked_entries as $entry) {
				if (!empty($entry) && $validate->is_number($entry) && $db->select('id', 'gallery', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1') {
					// Hochgeladene Bilder löschen
					$pictures = $db->select('file', 'galpics', 'gallery_id = \'' . $entry . '\'');
					foreach ($pictures as $row) {
						if (is_file('uploads/gallery/' . $row['file'])) {
							unlink('uploads/gallery/' . $row['file']);
						}
					}
					// Fotogalerie mitsamt Bildern löschen
					$bool = $db->delete('gallery', 'id = \'' . $entry . '\'');
					$bool2 = $db->delete('galpics', 'gallery_id = \'' . $entry . '\'', 0);

					// Galerie Cache löschen
					$cache->delete('gallery_pics_id_' . $entry);
				}
			}
			$content = combo_box($bool && $bool2 ? lang('gallery', 'delete_success') : lang('gallery', 'delete_error'), uri('gallery/acp_list'));
		} else {
			redirect('errors/404');
		}
		break;
	case 'acp_add_picture':
		$file['tmp_name'] = $_FILES['file']['tmp_name'];
		$file['name'] = $_FILES['file']['name'];
		$file['size'] = $_FILES['file']['size'];
		$form = $_POST['form'];

		if (!$validate->is_number($form['gallery']) || $db->select('id', 'gallery', 'id = \'' . $form['gallery'] . '\'', 0, 0, 0, 1) != '1')
			$errors[] = lang('gallery', 'no_gallery_selected');
		if (!$validate->is_number($form['pic']))
			$errors[] = lang('gallery', 'type_in_picture_number');
		if (empty($file['tmp_name']) || empty($file['size']))
			$errors[] = lang('gallery', 'no_picture_selected');
		if (!empty($file['tmp_name']) && !empty($file['size']) && !$validate->is_picture($file['tmp_name']))
			$errors[] = lang('gallery', 'only_png_jpg_gif_allowed');

		if (isset($errors)) {
			combo_box($errors);
		} else {
			$result = move_file($file['tmp_name'], $file['name'], 'gallery');

			$insert_values = array(
				'id' => '',
				'pic' => $form['pic'],
				'gallery_id' => $form['gallery'],
				'file' => $result['name'],
				'description' => $db->escape($form['description'], 2),
			);

			$bool = $db->insert('galpics', $insert_values);

			$cache->create('gallery_pics_id_' . $form['gallery'], $db->select('id', 'galpics', 'gallery_id = \'' . $modules->id . '\'', 'id ASC'));

			$content = combo_box($bool ? lang('gallery', 'add_picture_success') : lang('gallery', 'add_picture_error'), uri('gallery/acp_add_picture/id_' . $form['gallery'] . '/pic_' . ($pic + 1)));
		}
		break;
	case 'acp_edit_picture':
		if (!empty($_FILES['file']['tmp_name']) && $_FILES['file']['size'] > '0') {
			$file['tmp_name'] = $_FILES['file']['tmp_name'];
			$file['name'] = $_FILES['file']['name'];
			$file['size'] = $_FILES['file']['size'];
		}
		$form = $_POST['form'];

		if (!$validate->is_number($form['gallery']) || $db->select('id', 'gallery', 'id = \'' . $form['gallery'] . '\'', 0, 0, 0, 1) != '1')
			$errors[] = lang('gallery', 'no_gallery_selected');
		if (!$validate->is_number($form['pic']))
			$errors[] = lang('gallery', 'type_in_picture_number');
		if (isset($file) && is_array($file) && !$validate->is_picture($file['tmp_name']))
			$errors[] = lang('gallery', 'only_png_jpg_gif_allowed');

		if (isset($errors)) {
			combo_box($errors);
		} else {
			$new_file_sql = null;
			if (isset($file) && is_array($file)) {
				$result = move_file($file['tmp_name'], $file['name'], 'gallery');
				$new_file_sql = array('file' => $result['name'],);
			}

			$update_values = array(
				'pic' => $form['pic'],
				'gallery_id' => $form['gallery'],
				'description' => $db->escape($form['description'], 2),
			);
			if (is_array($new_file_sql)) {
				$update_values = array_merge($update_values, $new_file_sql);
			}

			$bool = $db->update('galpics', $update_values, 'id = \'' . $modules->id . '\'');

			$cache->create('gallery_pics_id_' . $form['gallery'], $db->select('id', 'galpics', 'gallery_id = \'' . $modules->id . '\'', 'id ASC'));

			$content = combo_box($bool ? lang('gallery', 'edit_picture_success') : lang('gallery', 'edit_picture_error'), uri('gallery/acp_list'));
		}
		break;
	case 'acp_delete_picture':
		if (isset($_POST['entries']) && is_array($_POST['entries']))
			$entries = $_POST['entries'];
		elseif (isset($modules->gen['entries']) && preg_match('/^([\d|]+)$/', $modules->gen['entries']))
			$entries = $modules->gen['entries'];

		if (is_array($entries)) {
			$marked_entries = '';
			foreach ($entries as $entry) {
				$marked_entries.= $entry . '|';
			}
			$content = combo_box(lang('gallery', 'confirm_picture_delete'), uri('gallery/acp_list/action_delete_picture/entries_' . $marked_entries), uri('gallery/acp_edit_gallery/id_' . $modules->id));
		} elseif (preg_match('/^([\d|]+)$/', $entries) && isset($modules->gen['confirmed'])) {
			$marked_entries = explode('|', $entries);
			$bool = 0;
			foreach ($marked_entries as $entry) {
				if (!empty($entry) && $validate->is_number($entry) && $db->select('id', 'galpics', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1')
					// Datei ebenfalls löschen
					$file = $db->select('file', 'galpics', 'id = \'' . $entry . '\'');
					if (is_file('uploads/gallery/' . $file[0]['file'])) {
						unlink('uploads/gallery/' . $file[0]['file']);
					}

					// Galerie Cache löschen
					$bool = $db->delete('galpics', 'id = \'' . $entry . '\'');
			}
			$content = combo_box($bool ? lang('gallery', 'picture_delete_success') : lang('gallery', 'picture_delete_error'), uri('gallery/acp_list'));
		} else {
			redirect('errors/404');
		}
		break;
	default:
		redirect('errors/404');
}
?>