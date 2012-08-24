<?php
/**
 * Newsletter
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['submit']) === true) {
	if (ACP3_Validate::email($_POST['mail']) === false)
		$errors['mail'] = $lang->t('common', 'wrong_email_format');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
	} else {
		$_POST['mailsig'] = $db->escape($_POST['mailsig']);

		$bool = ACP3_Config::setSettings('newsletter', $_POST);

		$session->unsetFormToken();

		setRedirectMessage($bool, $lang->t('common', $bool === true ? 'settings_success' : 'settings_error'), 'acp/newsletter');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$settings = ACP3_Config::getSettings('newsletter');
	$settings['mailsig'] = $db->escape($settings['mailsig'], 3);

	$tpl->assign('form', isset($_POST['submit']) ? $_POST : $settings);

	$session->generateFormToken();

	ACP3_View::setContent(ACP3_View::fetchTemplate('newsletter/acp_settings.tpl'));
}
