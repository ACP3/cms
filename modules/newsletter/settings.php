<?php
/**
 * Newsletter
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['form']) === true) {
	$form = $_POST['form'];

	if (!validate::email($form['mail']))
		$errors[] = $lang->t('common', 'wrong_email_format');
	if (!validate::formToken())
		$errors[] = $lang->t('common', 'form_already_submitted');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$form['mailsig'] = $db->escape($form['mailsig']);

		$bool = config::module('newsletter', $form);

		$session->unsetFormToken();

		setRedirectMessage($bool ? $lang->t('common', 'settings_success') : $lang->t('common', 'settings_error'), 'acp/newsletter');
	}
}
if (isset($_POST['form']) === false || isset($errors) === true && is_array($errors) === true) {
	$settings = config::getModuleSettings('newsletter');
	$settings['mailsig'] = $db->escape($settings['mailsig'], 3);

	$tpl->assign('form', isset($form) ? $form : $settings);

	$session->generateFormToken();

	view::setContent(view::fetchTemplate('newsletter/settings.tpl'));
}
