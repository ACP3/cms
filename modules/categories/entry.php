<?php
/**
 * Categories
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

		if (strlen($form['name']) < 3)
			$errors[] = lang('categories', 'name_to_short');
		if (strlen($form['description']) < 3)
			$errors[] = lang('categories', 'description_to_short');
		if (empty($form['module']))
			$errors[] = lang('categories', 'select_module');
		if (strlen($form['name']) > 3 && !empty($form['module']) && $db->select('id', 'categories', 'name = \'' . $db->escape($form['name']) . '\' AND module = \'' . $db->escape($form['module'], 2) . '\'', 0, 0, 0, 1) > 0)
			$errors[] = lang('categories', 'category_already_exists');

		if (isset($errors)) {
			$error_msg = combo_box($errors);
		} else {
			$insert_values = array(
				'id' => '',
				'name' => $db->escape($form['name']),
				'description' => $db->escape($form['description']),
				'module' => $db->escape($form['module'], 2),
			);

			$bool = $db->insert('categories', $insert_values);

			$cache->create('categories_' . $form['module'], $db->select('id, name, description', 'categories', 'module = \'' . $form['module'] . '\'', 'name ASC'));

			$content = combo_box($bool ? lang('categories', 'create_success') : lang('categories', 'create_error'), uri('acp/categories'));
		}
		break;
	case 'edit':
		$form = $_POST['form'];

		if (strlen($form['name']) < 3)
			$errors[] = lang('categories', 'name_to_short');
		if (strlen($form['description']) < 3)
			$errors[] = lang('categories', 'description_to_short');
		if (empty($form['module']))
			$errors[] = lang('categories', 'select_module');
		if (strlen($form['name']) > 3 && !empty($form['module']) && $db->select('id', 'categories', 'id != \'' . $modules->id . '\' AND name = \'' . $db->escape($form['name']) . '\' AND module = \'' . $db->escape($form['module'], 2) . '\'', 0, 0, 0, 1) > 0)
			$errors[] = lang('categories', 'category_already_exists');

		if (isset($errors)) {
			$error_msg = combo_box($errors);
		} else {
			$update_values = array(
				'name' => $db->escape($form['name']),
				'description' => $db->escape($form['description']),
				'module' => $db->escape($form['module'], 2),
			);
			$bool = $db->update('categories', $update_values, 'id = \'' . $modules->id . '\'');

			$cache->create('categories_' . $form['module'], $db->select('id, name, description', 'categories', 'module = \'' . $db->escape($form['module'], 2) . '\'', 'name ASC'));

			$content = combo_box($bool ? lang('categories', 'edit_success') : lang('categories', 'edit_error'), uri('acp/categories'));
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
			$content = combo_box(lang('categories', 'confirm_delete'), uri('acp/categories/adm_list/action_delete/entries_' . $marked_entries), uri('acp/categories'));
		} elseif (ereg('^([0-9|]+)$', $entries) && isset($modules->gen['confirmed'])) {
			$marked_entries = explode('|', $entries);
			$bool = 0;
			foreach ($marked_entries as $entry) {
				if (!empty($entry) && ereg('[0-9]', $entry) && $db->select('id', 'categories', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1') {
					$bool = $db->delete('categories', 'id = \'' . $entry . '\'');
				}
			}
			// Cache für die Kategorien neu erstellen
			$com_mods = $db->query('SELECT module FROM ' . CONFIG_DB_PRE . 'categories GROUP BY module');
			foreach ($com_mods as $row) {
				$cache->create('categories_' . $db->escape($row['module'], 3), $db->select('id, name, description', 'categories', 'module = \'' . $db->escape($row['module'], 3) . '\'', 'name ASC'));
			}
			$content = combo_box($bool ? lang('categories', 'delete_success') : lang('categories', 'delete_error'), uri('acp/categories'));
		} else {
			redirect('errors/404');
		}
		break;
	default:
		redirect('errors/404');
}
?>