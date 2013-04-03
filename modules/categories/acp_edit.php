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

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true &&
	ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'categories WHERE id = ?', array(ACP3_CMS::$uri->id)) == 1) {
	require_once MODULES_DIR . 'categories/functions.php';

	if (isset($_POST['submit']) === true) {
		if (!empty($_FILES['picture']['name'])) {
			$file['tmp_name'] = $_FILES['picture']['tmp_name'];
			$file['name'] = $_FILES['picture']['name'];
			$file['size'] = $_FILES['picture']['size'];
		}
		$settings = ACP3_Config::getSettings('categories');
		$module = ACP3_CMS::$db2->fetchAssoc('SELECT m.name FROM ' . DB_PRE . 'modules AS m JOIN ' . DB_PRE . 'categories AS c ON(m.id = c.module_id) WHERE c.id = ?', array(ACP3_CMS::$uri->id));

		if (strlen($_POST['title']) < 3)
			$errors['title'] = ACP3_CMS::$lang->t('categories', 'title_to_short');
		if (strlen($_POST['description']) < 3)
			$errors['description'] = ACP3_CMS::$lang->t('categories', 'description_to_short');
		if (!empty($file) &&
			(empty($file['tmp_name']) ||
			empty($file['size']) ||
			ACP3_Validate::isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize']) === false ||
			$_FILES['file']['error'] !== UPLOAD_ERR_OK))
			$errors['picture'] = ACP3_CMS::$lang->t('categories', 'invalid_image_selected');
		if (strlen($_POST['title']) >= 3 && categoriesCheckDuplicate($_POST['title'], $module['name'], ACP3_CMS::$uri->id))
			$errors['title'] = ACP3_CMS::$lang->t('categories', 'category_already_exists');

		if (isset($errors) === true) {
			ACP3_CMS::$view->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
		} else {
			$new_file_sql = null;
			if (isset($file) && is_array($file)) {
				$result = moveFile($file['tmp_name'], $file['name'], 'categories');
				$new_file_sql['picture'] = $result['name'];
			}

			$update_values = array(
				'title' => str_encode($_POST['title']),
				'description' => str_encode($_POST['description']),
			);
			if (is_array($new_file_sql) === true) {
				$old_file = ACP3_CMS::$db2->fetchColumn('SELECT picture FROM ' . DB_PRE . 'categories WEHRE id = ?', array(ACP3_CMS::$uri->id));
				removeUploadedFile('categories', $old_file);

				$update_values = array_merge($update_values, $new_file_sql);
			}

			$bool = ACP3_CMS::$db2->update(DB_PRE . 'categories', $update_values, array('id' => ACP3_CMS::$uri->id));

			setCategoriesCache($module['name']);

			ACP3_CMS::$session->unsetFormToken();

			setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/categories');
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$category = ACP3_CMS::$db2->fetchAssoc('SELECT title, description FROM ' . DB_PRE . 'categories WHERE id = ?', array(ACP3_CMS::$uri->id));

		ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : $category);

		ACP3_CMS::$session->generateFormToken();
	}
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}
