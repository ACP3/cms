<?php
/**
 * Categories
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (ACP3_Validate::isNumber($uri->id) === true && $db->countRows('*', 'categories', 'id = \'' . $uri->id . '\'') == 1) {
	require_once MODULES_DIR . 'categories/functions.php';

	if (isset($_POST['submit']) === true) {
		if (!empty($_FILES['picture']['name'])) {
			$file['tmp_name'] = $_FILES['picture']['tmp_name'];
			$file['name'] = $_FILES['picture']['name'];
			$file['size'] = $_FILES['picture']['size'];
		}
		$settings = ACP3_Config::getModuleSettings('categories');
		$module = $db->select('module', 'categories', 'id = \'' . $uri->id . '\'');

		if (strlen($_POST['name']) < 3)
			$errors['name'] = $lang->t('categories', 'name_to_short');
		if (strlen($_POST['description']) < 3)
			$errors['description'] = $lang->t('categories', 'description_to_short');
		if (!empty($file) &&
			(empty($file['tmp_name']) ||
			empty($file['size']) ||
			ACP3_Validate::isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize']) === false ||
			$_FILES['file']['error'] !== UPLOAD_ERR_OK))
			$errors['picture'] = $lang->t('categories', 'invalid_image_selected');
		if (strlen($_POST['name']) >= 3 && categoriesCheckDuplicate($db->escape($_POST['name']), $module[0]['module'], $uri->id))
			$errors['name'] = $lang->t('categories', 'category_already_exists');

		if (isset($errors) === true) {
			$tpl->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
		} else {
			$new_file_sql = null;
			if (isset($file) && is_array($file)) {
				$result = moveFile($file['tmp_name'], $file['name'], 'categories');
				$new_file_sql['picture'] = $result['name'];
			}

			$update_values = array(
				'name' => $db->escape($_POST['name']),
				'description' => $db->escape($_POST['description']),
			);
			if (is_array($new_file_sql) === true) {
				$old_file = $db->select('picture', 'categories', 'id = \'' . $uri->id . '\'');
				removeUploadedFile('categories', $old_file[0]['picture']);

				$update_values = array_merge($update_values, $new_file_sql);
			}

			$bool = $db->update('categories', $update_values, 'id = \'' . $uri->id . '\'');

			setCategoriesCache($db->escape($module[0]['module'], 3));

			$session->unsetFormToken();

			setRedirectMessage($bool !== false ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), 'acp/categories');
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$category = $db->select('name, description', 'categories', 'id = \'' . $uri->id . '\'');
		$category[0]['name'] = $db->escape($category[0]['name'], 3);
		$category[0]['description'] = $db->escape($category[0]['description'], 3);

		$tpl->assign('form', isset($_POST['submit']) ? $_POST : $category[0]);

		$session->generateFormToken();

		ACP3_View::setContent(ACP3_View::fetchTemplate('categories/acp_edit.tpl'));
	}
} else {
	$uri->redirect('errors/404');
}
