<?php
/**
 * Emoticons
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

require_once MODULES_DIR . 'emoticons/functions.php';

if (isset($_POST['submit']) === true) {
	if (!empty($_FILES['picture']['tmp_name'])) {
		$file['tmp_name'] = $_FILES['picture']['tmp_name'];
		$file['name'] = $_FILES['picture']['name'];
		$file['size'] = $_FILES['picture']['size'];
	}
	$settings = ACP3_Config::getSettings('emoticons');

	if (empty($_POST['code']))
		$errors['code'] = $lang->t('emoticons', 'type_in_code');
	if (empty($_POST['description']))
		$errors['description'] = $lang->t('emoticons', 'type_in_description');
	if (!isset($file) ||
		ACP3_Validate::isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize']) === false ||
		$_FILES['picture']['error'] !== UPLOAD_ERR_OK)
		$errors['picture'] = $lang->t('emoticons', 'invalid_image_selected');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
	} else {
		$result = moveFile($file['tmp_name'], $file['name'], 'emoticons');

		$insert_values = array(
			'id' => '',
			'code' => $db->escape($_POST['code']),
			'description' => $db->escape($_POST['description']),
			'img' => $result['name'],
		);

		$bool = $db->insert('emoticons', $insert_values);
		setEmoticonsCache();

		$session->unsetFormToken();

		setRedirectMessage($bool, $lang->t('common', $bool !== false ? 'create_success' : 'create_error'), 'acp/emoticons');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$tpl->assign('form', isset($_POST['submit']) ? $_POST : array('code' => '', 'description' => ''));

	$session->generateFormToken();

	ACP3_View::setContent(ACP3_View::fetchTemplate('emoticons/acp_create.tpl'));
}
