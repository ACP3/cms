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

require_once MODULES_DIR . 'categories/functions.php';

if (isset($_POST['submit']) === true) {
	if (!empty($_FILES['picture']['name'])) {
		$file['tmp_name'] = $_FILES['picture']['tmp_name'];
		$file['name'] = $_FILES['picture']['name'];
		$file['size'] = $_FILES['picture']['size'];
	}
	$settings = ACP3_Config::getSettings('categories');

	if (strlen($_POST['title']) < 3)
		$errors['title'] = ACP3_CMS::$lang->t('categories', 'title_to_short');
	if (strlen($_POST['description']) < 3)
		$errors['description'] = ACP3_CMS::$lang->t('categories', 'description_to_short');
	if (!empty($file) &&
		(empty($file['tmp_name']) ||
		empty($file['size']) ||
		ACP3_Validate::isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize']) === false ||
		$_FILES['picture']['error'] !== UPLOAD_ERR_OK))
		$errors['picture'] = ACP3_CMS::$lang->t('categories', 'invalid_image_selected');
	if (empty($_POST['module']))
		$errors['module'] = ACP3_CMS::$lang->t('categories', 'select_module');
	if (strlen($_POST['title']) >= 3 && categoriesCheckDuplicate($_POST['title'], $_POST['module']))
		$errors['title'] = ACP3_CMS::$lang->t('categories', 'category_already_exists');

	if (isset($errors) === true) {
		ACP3_CMS::$view->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
	} else {
		$file_sql = null;
		if (!empty($file)) {
			$result = moveFile($file['tmp_name'], $file['name'], 'categories');
			$file_sql = array('picture' => $result['name']);
		}

		$mod_id = ACP3_CMS::$db2->fetchColumn('SELECT id FROM ' . DB_PRE . 'modules WHERE name = ?', array($_POST['module']));
		$insert_values = array(
			'id' => '',
			'title' => str_encode($_POST['title']),
			'description' => str_encode($_POST['description']),
			'module_id' => $mod_id,
		);
		if (is_array($file_sql) === true) {
			$insert_values = array_merge($insert_values, $file_sql);
		}

		$bool = ACP3_CMS::$db2->insert(DB_PRE . 'categories', $insert_values);
		setCategoriesCache($_POST['module']);

		ACP3_CMS::$session->unsetFormToken();

		setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/categories');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : array('title' => '', 'description' => ''));

	$mod_list = ACP3_Modules::getActiveModules();
	foreach ($mod_list as $name => $info) {
		if ($info['active'] && in_array('categories', $info['dependencies']) === true) {
			$mod_list[$name]['selected'] = selectEntry('module', $info['dir']);
		} else {
			unset($mod_list[$name]);
		}
	}
	ACP3_CMS::$view->assign('mod_list', $mod_list);

	ACP3_CMS::$session->generateFormToken();
}
