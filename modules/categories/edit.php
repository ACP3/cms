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

if (!empty($modules->id) && $db->select('id', 'categories', 'id = \'' . $modules->id . '\'', 0, 0, 0, 1) == '1') {
	if (isset($_POST['submit'])) {
		$form = $_POST['form'];
		if (!empty($_FILES['picture']['name'])) {
			$file['tmp_name'] = $_FILES['picture']['tmp_name'];
			$file['name'] = $_FILES['picture']['name'];
			$file['size'] = $_FILES['picture']['size'];
		}
		$settings = $config->output('categories');

		if (strlen($form['name']) < 3)
			$errors[] = lang('categories', 'name_to_short');
		if (strlen($form['description']) < 3)
			$errors[] = lang('categories', 'description_to_short');
		if (!empty($file) && (empty($file['tmp_name']) || empty($file['size']) || !$validate->isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize'])))
			$errors[] = lang('categories', 'invalid_image_selected');
		if (strlen($form['name']) > 3 && !empty($form['module']) && $db->select('id', 'categories', 'id != \'' . $modules->id . '\' AND name = \'' . $db->escape($form['name']) . '\' AND module = \'' . $db->escape($form['module'], 2) . '\'', 0, 0, 0, 1) > 0)
			$errors[] = lang('categories', 'category_already_exists');

		if (isset($errors)) {
			$tpl->assign('error_msg', comboBox($errors));
		} else {
			$new_file_sql = null;
			if (isset($file) && is_array($file)) {
				$result = moveFile($file['tmp_name'], $file['name'], 'categories');
				$new_file_sql['picture'] = $result['name'];
			}

			$update_values = array(
				'name' => $db->escape($form['name']),
				'description' => $db->escape($form['description']),
			);
			if (is_array($new_file_sql)) {
				$old_file = $db->select('picture', 'categories', 'id = \'' . $modules->id . '\'');
				removeFile('categories', $old_file[0]['picture']);

				$update_values = array_merge($update_values, $new_file_sql);
			}

			$bool = $db->update('categories', $update_values, 'id = \'' . $modules->id . '\'');

			$category = $db->select('module', 'categories', 'id = \'' . $modules->id . '\'');

			$cache->create('categories_' . $db->escape($category[0]['module'], 3), $db->select('id, name, picture, description', 'categories', 'module = \'' . $db->escape($category[0]['module'], 3) . '\'', 'name ASC'));

			$content = comboBox($bool ? lang('categories', 'edit_success') : lang('categories', 'edit_error'), uri('acp/categories'));
		}
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$category = $db->select('name, description', 'categories', 'id = \'' . $modules->id . '\'');

		$tpl->assign('form', isset($form) ? $form : $category[0]);

		$content = $tpl->fetch('categories/edit.html');
	}
} else {
	redirect('errors/404');
}
?>