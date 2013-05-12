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

if (ACP3\Core\Validate::isNumber(ACP3\CMS::$injector['URI']->id) === true &&
	ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'categories WHERE id = ?', array(ACP3\CMS::$injector['URI']->id)) == 1) {
	require_once MODULES_DIR . 'categories/functions.php';

	if (isset($_POST['submit']) === true) {
		if (!empty($_FILES['picture']['name'])) {
			$file['tmp_name'] = $_FILES['picture']['tmp_name'];
			$file['name'] = $_FILES['picture']['name'];
			$file['size'] = $_FILES['picture']['size'];
		}
		$settings = ACP3\Core\Config::getSettings('categories');
		$module = ACP3\CMS::$injector['Db']->fetchAssoc('SELECT m.name FROM ' . DB_PRE . 'modules AS m JOIN ' . DB_PRE . 'categories AS c ON(m.id = c.module_id) WHERE c.id = ?', array(ACP3\CMS::$injector['URI']->id));

		if (strlen($_POST['title']) < 3)
			$errors['title'] = ACP3\CMS::$injector['Lang']->t('categories', 'title_to_short');
		if (strlen($_POST['description']) < 3)
			$errors['description'] = ACP3\CMS::$injector['Lang']->t('categories', 'description_to_short');
		if (!empty($file) &&
			(empty($file['tmp_name']) ||
			empty($file['size']) ||
			ACP3\Core\Validate::isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize']) === false ||
			$_FILES['file']['error'] !== UPLOAD_ERR_OK))
			$errors['picture'] = ACP3\CMS::$injector['Lang']->t('categories', 'invalid_image_selected');
		if (strlen($_POST['title']) >= 3 && categoriesCheckDuplicate($_POST['title'], $module['name'], ACP3\CMS::$injector['URI']->id))
			$errors['title'] = ACP3\CMS::$injector['Lang']->t('categories', 'category_already_exists');

		if (isset($errors) === true) {
			ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
		} elseif (ACP3\Core\Validate::formToken() === false) {
			ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted')));
		} else {
			$new_file_sql = null;
			if (isset($file) && is_array($file)) {
				$result = moveFile($file['tmp_name'], $file['name'], 'categories');
				$new_file_sql['picture'] = $result['name'];
			}

			$update_values = array(
				'title' => ACP3\Core\Functions::str_encode($_POST['title']),
				'description' => ACP3\Core\Functions::str_encode($_POST['description']),
			);
			if (is_array($new_file_sql) === true) {
				$old_file = ACP3\CMS::$injector['Db']->fetchColumn('SELECT picture FROM ' . DB_PRE . 'categories WEHRE id = ?', array(ACP3\CMS::$injector['URI']->id));
				removeUploadedFile('categories', $old_file);

				$update_values = array_merge($update_values, $new_file_sql);
			}

			$bool = ACP3\CMS::$injector['Db']->update(DB_PRE . 'categories', $update_values, array('id' => ACP3\CMS::$injector['URI']->id));

			setCategoriesCache($module['name']);

			ACP3\CMS::$injector['Session']->unsetFormToken();

			ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/categories');
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$category = ACP3\CMS::$injector['Db']->fetchAssoc('SELECT title, description FROM ' . DB_PRE . 'categories WHERE id = ?', array(ACP3\CMS::$injector['URI']->id));

		ACP3\CMS::$injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $category);

		ACP3\CMS::$injector['Session']->generateFormToken();
	}
} else {
	ACP3\CMS::$injector['URI']->redirect('errors/404');
}
