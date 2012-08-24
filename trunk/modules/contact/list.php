<?php
/**
 * Contact
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

if (isset($_POST['submit']) === true) {
	if (empty($_POST['name']))
		$errors['name'] = $lang->t('common', 'name_to_short');
	if (ACP3_Validate::email($_POST['mail']) === false)
		$errors['mail'] = $lang->t('common', 'wrong_email_format');
	if (strlen($_POST['message']) < 3)
		$errors['message'] = $lang->t('common', 'message_to_short');
	if ($auth->isUser() === false && ACP3_Validate::captcha($_POST['captcha']) === false)
		$errors['captcha'] = $lang->t('captcha', 'invalid_captcha_entered');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
	} else {
		$settings = ACP3_Config::getSettings('contact');

		$subject = sprintf($lang->t('contact', 'contact_subject'), CONFIG_SEO_TITLE);
		$body = str_replace(array('{name}', '{mail}', '{message}', '\n'), array($_POST['name'], $_POST['mail'], $_POST['message'], "\n"), $lang->t('contact', 'contact_body'));
		$bool = generateEmail('', $settings['mail'], $_POST['mail'], $subject, $body);

		// Nachrichtenkopie an Absender senden
		if (isset($_POST['copy'])) {
			$subject2 = sprintf($lang->t('contact', 'sender_subject'), CONFIG_SEO_TITLE);
			$body2 = sprintf($lang->t('contact', 'sender_body'), CONFIG_SEO_TITLE, $_POST['message']);
			generateEmail($_POST['name'], $_POST['mail'], $settings['mail'], $subject2, $body2);
		}

		$session->unsetFormToken();

		ACP3_View::setContent(confirmBox($bool === true ? $lang->t('contact', 'send_mail_success') : $lang->t('contact', 'send_mail_error'), $uri->route('contact')));
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	// Falls Benutzer eingeloggt ist, Formular schon teilweise ausfüllen
	if ($auth->isUser() === true) {
		$defaults = $auth->getUserInfo();
		$disabled = ' readonly="readonly" class="readonly"';
		$defaults['name'] = !empty($defaults['realname']) ? $db->escape($defaults['realname'], 3) : $db->escape($defaults['nickname'], 3);
		$defaults['message'] = '';

		if (isset($_POST['submit'])) {
			$_POST['name_disabled'] = $disabled;
			$_POST['mail_disabled'] = $disabled;
		} else {
			$defaults['name_disabled'] = $disabled;
			$defaults['mail_disabled'] = $disabled;
		}
	} else {
		$defaults = array(
			'name' => '',
			'name_disabled' => '',
			'mail' => '',
			'mail_disabled' => '',
			'message' => '',
		);
	}
	$tpl->assign('form', isset($_POST['submit']) ? array_merge($defaults, $_POST) : $defaults);
	$tpl->assign('copy_checked', selectEntry('copy', 1, 0, 'checked'));

	require_once MODULES_DIR . 'captcha/functions.php';
	$tpl->assign('captcha', captcha());

	$session->generateFormToken();

	ACP3_View::setContent(ACP3_View::fetchTemplate('contact/list.tpl'));
}
