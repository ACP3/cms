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

if (isset($_POST['form']) === true) {
	$form = $_POST['form'];

	if (validate::isNumber($form['width']) === false)
		$errors[] = $lang->t('emoticons', 'invalid_image_width_entered');
	if (validate::isNumber($form['height']) === false)
		$errors[] = $lang->t('emoticons', 'invalid_image_height_entered');
	if (validate::isNumber($form['filesize']) === false)
		$errors[] = $lang->t('emoticons', 'invalid_image_filesize_entered');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (validate::formToken() === false) {
		view::setContent(errorBox($lang->t('common', 'form_already_submitted')));
	} else {
		$bool = config::module('emoticons', $form);

		$session->unsetFormToken();

		setRedirectMessage($bool === true ? $lang->t('common', 'settings_success') : $lang->t('common', 'settings_error'), 'acp/emoticons');
	}
}
if (isset($_POST['form']) === false || isset($errors) === true && is_array($errors) === true) {
	$settings = config::getModuleSettings('emoticons');

	$tpl->assign('form', isset($form) ? $form : $settings);

	$session->generateFormToken();

	view::setContent(view::fetchTemplate('emoticons/settings.tpl'));
}
