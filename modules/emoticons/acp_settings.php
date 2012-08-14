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
		$errors['width'] = $lang->t('emoticons', 'invalid_image_width_entered');
	if (ACP3_Validate::isNumber($_POST['height']) === false)
		$errors['height'] = $lang->t('emoticons', 'invalid_image_height_entered');
	if (ACP3_Validate::isNumber($_POST['filesize']) === false)
		$errors['filesize'] = $lang->t('emoticons', 'invalid_image_filesize_entered');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
	} else {
		$bool = ACP3_Config::module('emoticons', $_POST);

		$session->unsetFormToken();

		setRedirectMessage($bool, $lang->t('common', $bool === true ? 'settings_success' : 'settings_error'), 'acp/emoticons');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$settings = ACP3_Config::getModuleSettings('emoticons');

	$tpl->assign('form', isset($_POST['submit']) ? $_POST : $settings);

	$session->generateFormToken();

	ACP3_View::setContent(ACP3_View::fetchTemplate('emoticons/acp_settings.tpl'));
}
