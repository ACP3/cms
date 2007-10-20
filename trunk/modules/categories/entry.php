<?php
/**
 * Categories
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;
if (!$modules->check('categories', 'entry'))
	redirect('errors/403');

switch ($modules->action) {
	case 'acp_create':
		$form = $_POST['form'];

		if (strlen($form['name']) < 3)
			$errors[] = lang('categories', 'name_to_short');
		if (strlen($form['description']) < 3)
			$errors[] = lang('categories', 'description_to_short');
		if (empty($form['module']))
			$errors[] = lang('categories', 'select_module');
		if (strlen($form['name']) > 3 && !empty($form['module']) && $db->select('id', 'categories', 'name = \'' . $db->escape($form['name']) . '\' AND module = \'' . $db->escape($form['module'], 2) . '\'', 0, 0, 0, 1) > 0)
			$errors[] = lang('categories', 'category_already_exists');

		if (isset($errors)) {
			combo_box($errors);
		} else {
			$insert_values = array(
				'id' => '',
				'name' => $db->escape($form['name']),
				'description' => $db->escape($form['description']),
				'module' => $db->escape($form['module'], 2),
			);

			$bool = $db->insert('categories', $insert_values);

			$cache->create('categories_' . $form['module'], $db->select('id, name, description', 'categories', 'module = \'' . $form['module'] . '\'', 'name ASC'));

			$content = combo_box($bool ? lang('categories', 'create_success') : lang('categories', 'create_error'), uri('categories/acp_list'));
		}
		break;
	case 'acp_edit':
		$form = $_POST['form'];

		if (strlen($form['name']) < 3)
			$errors[] = lang('categories', 'name_to_short');
		if (strlen($form['description']) < 3)
			$errors[] = lang('categories', 'description_to_short');
		if (strlen($form['name']) > 3 && !empty($form['module']) && $db->select('id', 'categories', 'id != \'' . $modules->id . '\' AND name = \'' . $db->escape($form['name']) . '\' AND module = \'' . $db->escape($form['module'], 2) . '\'', 0, 0, 0, 1) > 0)
			$errors[] = lang('categories', 'category_already_exists');

		if (isset($errors)) {
			combo_box($errors);
		} else {
			$update_values = array(
				'name' => $db->escape($form['name']),
				'description' => $db->escape($form['description']),
			);
			$bool = $db->update('categories', $update_values, 'id = \'' . $modules->id . '\'');

			$category = $db->select('module', 'categories', 'id = \'' . $modules->id . '\'');

			$cache->create('categories_' . $db->escape($category[0]['module'], 3), $db->select('id, name, description', 'categories', 'module = \'' . $db->escape($category[0]['module'], 3) . '\'', 'name ASC'));

			$content = combo_box($bool ? lang('categories', 'edit_success') : lang('categories', 'edit_error'), uri('categories/acp_list'));
		}
		break;
	case 'acp_delete':
		if (isset($_POST['entries']) && is_array($_POST['entries']))
			$entries = $_POST['entries'];
		elseif (isset($modules->gen['entries']) && preg_match('/^([\d|]+)$/', $modules->gen['entries']))
			$entries = $modules->gen['entries'];

		if (is_array($entries)) {
			$marked_entries = '';
			foreach ($entries as $entry) {
				$marked_entries.= $entry . '|';
			}
			$content = combo_box(lang('categories', 'confirm_delete'), uri('categories/acp_list/action_delete/entries_' . $marked_entries), uri('categories/acp_list'));
		} elseif (preg_match('/^([\d|]+)$/', $entries) && isset($modules->gen['confirmed'])) {
			$marked_entries = explode('|', $entries);
			$bool = 0;
			$in_use = 0;

			foreach ($marked_entries as $entry) {
				if (!empty($entry) && $validate->is_number($entry) && $db->select('id', 'categories', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1') {
					$category = $db->select('module', 'categories', 'id = \'' . $entry . '\'');
					$c_in_use = $db->select('id', $db->escape($category[0]['module'], 3), 'category_id = \'' . $entry . '\'', 0, 0, 0, 1);
					if ($c_in_use > 0) {
						$in_use = 1;
					} else {
						$bool = $db->delete('categories', 'id = \'' . $entry . '\'');
					}
				}
			}
			// Cache für die Kategorien neu erstellen
			$com_mods = $db->query('SELECT module FROM ' . CONFIG_DB_PRE . 'categories GROUP BY module');
			foreach ($com_mods as $row) {
				$cache->create('categories_' . $db->escape($row['module'], 3), $db->select('id, name, description', 'categories', 'module = \'' . $db->escape($row['module'], 3) . '\'', 'name ASC'));
			}

			if ($in_use) {
				$text = lang('categories', 'category_is_in_use');
			} else {
				$text = $bool ? lang('categories', 'delete_success') : lang('categories', 'delete_error');
			}
			$content = combo_box($text, uri('categories/acp_list'));
		} else {
			redirect('errors/404');
		}
		break;
	default:
		redirect('errors/404');
}
?>