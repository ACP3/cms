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

if (isset($_POST['submit']) === true) {
	if (ACP3_Validate::isNumber($_POST['width']) === false)
		$errors['width'] = ACP3_CMS::$lang->t('emoticons', 'invalid_image_width_entered');
	if (ACP3_Validate::isNumber($_POST['height']) === false)
		$errors['height'] = ACP3_CMS::$lang->t('emoticons', 'invalid_image_height_entered');
	if (ACP3_Validate::isNumber($_POST['filesize']) === false)
		$errors['filesize'] = ACP3_CMS::$lang->t('emoticons', 'invalid_image_filesize_entered');

	if (isset($errors) === true) {
		ACP3_CMS::$view->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
	} else {
		$data = array(
			'width' => (int) $_POST['width'],
			'height' => (int) $_POST['height'],
			'filesize' => (int) $_POST['filesize'],
		);
		$bool = ACP3_Config::setSettings('emoticons', $data);

		ACP3_CMS::$session->unsetFormToken();

		setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/emoticons');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$settings = ACP3_Config::getSettings('emoticons');

	ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : $settings);

	ACP3_CMS::$session->generateFormToken();
}
