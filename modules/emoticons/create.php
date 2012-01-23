<?php
/**
 * Emoticons
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

require_once MODULES_DIR . 'emoticons/functions.php';

if (isset($_POST['form'])) {
	$form = $_POST['form'];
	if (!empty($_FILES['picture']['tmp_name'])) {
		$file['tmp_name'] = $_FILES['picture']['tmp_name'];
		$file['name'] = $_FILES['picture']['name'];
		$file['size'] = $_FILES['picture']['size'];
	}
	$settings = config::getModuleSettings('emoticons');

	if (empty($form['code']))
		$errors[] = $lang->t('emoticons', 'type_in_code');
	if (empty($form['description']))
		$errors[] = $lang->t('emoticons', 'type_in_description');
	if (!isset($file) ||
		!validate::isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize']) ||
		$_FILES['picture']['error'] !== UPLOAD_ERR_OK)
		$errors[] = $lang->t('emoticons', 'invalid_image_selected');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$result = moveFile($file['tmp_name'], $file['name'], 'emoticons');

		$insert_values = array(
		'id' => '',
		'code' => $db->escape($form['code']),
		'description' => $db->escape($form['description']),
		'img' => $result['name'],
		);

		$bool = $db->insert('emoticons', $insert_values);
		setEmoticonsCache();

		view::setContent(comboBox($bool ? $lang->t('common', 'create_success') : $lang->t('common', 'create_error'), $uri->route('acp/emoticons')));
	}
}
if (!isset($_POST['form']) || isset($errors) && is_array($errors)) {
	$tpl->assign('form', isset($form) ? $form : array('code' => '', 'description' => ''));

	view::setContent(view::fetchTemplate('emoticons/create.tpl'));
}
