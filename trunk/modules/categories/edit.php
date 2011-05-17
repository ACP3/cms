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

if (validate::isNumber($uri->id) && $db->countRows('*', 'categories', 'id = \'' . $uri->id . '\'') == '1') {
	require_once ACP3_ROOT . 'modules/categories/functions.php';

	if (isset($_POST['form'])) {
		$form = $_POST['form'];
		if (!empty($_FILES['picture']['name'])) {
			$file['tmp_name'] = $_FILES['picture']['tmp_name'];
			$file['name'] = $_FILES['picture']['name'];
			$file['size'] = $_FILES['picture']['size'];
		}
		$settings = config::output('categories');
		$module = $db->select('module', 'categories', 'id = \'' . $uri->id . '\'');

		if (strlen($form['name']) < 3)
			$errors[] = $lang->t('categories', 'name_to_short');
		if (strlen($form['description']) < 3)
			$errors[] = $lang->t('categories', 'description_to_short');
		if (!empty($file) && (empty($file['tmp_name']) || empty($file['size']) || !validate::isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize'])))
			$errors[] = $lang->t('categories', 'invalid_image_selected');
		if (strlen($form['name']) >= 3 && categoriesCheckDuplicate($db->escape($form['name']), $module[0]['module'], $uri->id))
			$errors[] = $lang->t('categories', 'category_already_exists');

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
				$old_file = $db->select('picture', 'categories', 'id = \'' . $uri->id . '\'');
				removeFile('categories', $old_file[0]['picture']);

				$update_values = array_merge($update_values, $new_file_sql);
			}

			$bool = $db->update('categories', $update_values, 'id = \'' . $uri->id . '\'');

			setCategoriesCache($db->escape($module[0]['module'], 3));

			$content = comboBox($bool !== null ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), uri('acp/categories'));
		}
	}
	if (!isset($_POST['form']) || isset($errors) && is_array($errors)) {
		$category = $db->select('name, description', 'categories', 'id = \'' . $uri->id . '\'');
		$tpl->assign('form', isset($form) ? $form : $category[0]);

		$content = modules::fetchTemplate('categories/edit.html');
	}
} else {
	redirect('errors/404');
}
