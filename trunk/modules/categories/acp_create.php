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
	$settings = ACP3_Config::getModuleSettings('categories');

	if (strlen($_POST['name']) < 3)
		$errors['name'] = $lang->t('categories', 'name_to_short');
	if (strlen($_POST['description']) < 3)
		$errors['description'] = $lang->t('categories', 'description_to_short');
	if (!empty($file) &&
		(empty($file['tmp_name']) ||
		empty($file['size']) ||
		ACP3_Validate::isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize']) === false ||
		$_FILES['picture']['error'] !== UPLOAD_ERR_OK))
		$errors['picture'] = $lang->t('categories', 'invalid_image_selected');
	if (empty($_POST['module']))
		$errors['module'] = $lang->t('categories', 'select_module');
	if (strlen($_POST['name']) >= 3 && categoriesCheckDuplicate($db->escape($_POST['name']), $_POST['module']))
		$errors['name'] = $lang->t('categories', 'category_already_exists');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
	} else {
		$file_sql = null;
		if (!empty($file)) {
			$result = moveFile($file['tmp_name'], $file['name'], 'categories');
			$file_sql = array('picture' => $result['name']);
		}

		$insert_values = array(
			'id' => '',
			'name' => $db->escape($_POST['name']),
			'description' => $db->escape($_POST['description']),
			'module' => $db->escape($_POST['module'], 2),
		);
		if (is_array($file_sql) === true) {
			$insert_values = array_merge($insert_values, $file_sql);
		}

		$bool = $db->insert('categories', $insert_values);
		setCategoriesCache($_POST['module']);

		$session->unsetFormToken();

		setRedirectMessage($bool, $lang->t('common', $bool !== false ? 'create_success' : 'create_error'), 'acp/categories');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$tpl->assign('form', isset($_POST['submit']) ? $_POST : array('name' => '', 'description' => ''));

	$mod_list = ACP3_Modules::getAllModules();

	foreach ($mod_list as $name => $info) {
		if ($info['active'] && $info['categories']) {
			$mod_list[$name]['selected'] = selectEntry('module', $info['dir']);
		} else {
			unset($mod_list[$name]);
		}
	}
	$tpl->assign('mod_list', $mod_list);

	$session->generateFormToken();

	ACP3_View::setContent(ACP3_View::fetchTemplate('categories/acp_create.tpl'));
}
