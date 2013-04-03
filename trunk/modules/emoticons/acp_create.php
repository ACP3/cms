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
		$errors['code'] = ACP3_CMS::$lang->t('emoticons', 'type_in_code');
	if (empty($_POST['description']))
		$errors['description'] = ACP3_CMS::$lang->t('emoticons', 'type_in_description');
	if (!isset($file) ||
		ACP3_Validate::isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize']) === false ||
		$_FILES['picture']['error'] !== UPLOAD_ERR_OK)
		$errors['picture'] = ACP3_CMS::$lang->t('emoticons', 'invalid_image_selected');

	if (isset($errors) === true) {
		ACP3_CMS::$view->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
	} else {
		$result = moveFile($file['tmp_name'], $file['name'], 'emoticons');

		$insert_values = array(
			'id' => '',
			'code' => str_encode($_POST['code']),
			'description' => str_encode($_POST['description']),
			'img' => $result['name'],
		);

		$bool = ACP3_CMS::$db2->insert(DB_PRE . 'emoticons', $insert_values);
		setEmoticonsCache();

		ACP3_CMS::$session->unsetFormToken();

		setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/emoticons');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : array('code' => '', 'description' => ''));

	ACP3_CMS::$session->generateFormToken();
}
