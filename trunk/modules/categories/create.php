<?php
/**
 * Categories
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

require_once MODULES_DIR . 'categories/functions.php';

if (isset($_POST['form']) === true) {
	$form = $_POST['form'];
	if (!empty($_FILES['picture']['name'])) {
		$file['tmp_name'] = $_FILES['picture']['tmp_name'];
		$file['name'] = $_FILES['picture']['name'];
		$file['size'] = $_FILES['picture']['size'];
	}
	$settings = config::getModuleSettings('categories');

	if (strlen($form['name']) < 3)
		$errors[] = $lang->t('categories', 'name_to_short');
	if (strlen($form['description']) < 3)
		$errors[] = $lang->t('categories', 'description_to_short');
	if (!empty($file) &&
		(empty($file['tmp_name']) ||
		empty($file['size']) ||
		!validate::isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize']) ||
		$_FILES['picture']['error'] !== UPLOAD_ERR_OK))
		$errors[] = $lang->t('categories', 'invalid_image_selected');
	if (empty($form['module']))
		$errors[] = $lang->t('categories', 'select_module');
	if (strlen($form['name']) >= 3 && categoriesCheckDuplicate($db->escape($form['name']), $form['module']))
		$errors[] = $lang->t('categories', 'category_already_exists');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', comboBox($errors));
	} elseif (!validate::formToken()) {
		view::setContent(comboBox($lang->t('common', 'form_already_submitted')));
	} else {
		$file_sql = null;
		if (!empty($file)) {
			$result = moveFile($file['tmp_name'], $file['name'], 'categories');
			$file_sql = array('picture' => $result['name']);
		}

		$insert_values = array(
			'id' => '',
			'name' => $db->escape($form['name']),
			'description' => $db->escape($form['description']),
			'module' => $db->escape($form['module'], 2),
		);
		if (is_array($file_sql)) {
			$insert_values = array_merge($insert_values, $file_sql);
		}

		$bool = $db->insert('categories', $insert_values);
		setCategoriesCache($form['module']);

		$session->unsetFormToken();

		view::setContent(comboBox($bool ? $lang->t('common', 'create_success') : $lang->t('common', 'create_error'), $uri->route('acp/categories')));
	}
}
if (isset($_POST['form']) === false || isset($errors) === true && is_array($errors) === true) {
	$tpl->assign('form', isset($form) ? $form : array('name' => '', 'description' => ''));

	$mod_list = modules::modulesList();

	foreach ($mod_list as $name => $info) {
		if ($info['active'] && $info['categories']) {
			$mod_list[$name]['selected'] = selectEntry('module', $info['dir']);
		} else {
			unset($mod_list[$name]);
		}
	}
	$tpl->assign('mod_list', $mod_list);

	$session->generateFormToken();

	view::setContent(view::fetchTemplate('categories/create.tpl'));
}
