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
		$errors['mail'] = ACP3_CMS::$lang->t('common', 'wrong_email_format');

	if (isset($errors) === true) {
		ACP3_CMS::$view->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('common', 'form_already_submitted')));
	} else {
		$_POST['mailsig'] = ACP3_CMS::$db->escape($_POST['mailsig']);

		$bool = ACP3_Config::setSettings('newsletter', $_POST);

		ACP3_CMS::$session->unsetFormToken();

		setRedirectMessage($bool, ACP3_CMS::$lang->t('common', $bool === true ? 'settings_success' : 'settings_error'), 'acp/newsletter');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$settings = ACP3_Config::getSettings('newsletter');
	$settings['mailsig'] = ACP3_CMS::$db->escape($settings['mailsig'], 3);

	ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : $settings);

	ACP3_CMS::$session->generateFormToken();

	ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('newsletter/acp_settings.tpl'));
}
