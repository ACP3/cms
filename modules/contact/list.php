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

$captchaAccess = ACP3_Modules::check('captcha', 'functions');

if (isset($_POST['submit']) === true) {
	if (empty($_POST['name']))
		$errors['name'] = ACP3_CMS::$lang->t('system', 'name_to_short');
	if (ACP3_Validate::email($_POST['mail']) === false)
		$errors['mail'] = ACP3_CMS::$lang->t('system', 'wrong_email_format');
	if (strlen($_POST['message']) < 3)
		$errors['message'] = ACP3_CMS::$lang->t('system', 'message_to_short');
	if ($captchaAccess === true && ACP3_CMS::$auth->isUser() === false && ACP3_Validate::captcha($_POST['captcha']) === false)
		$errors['captcha'] = ACP3_CMS::$lang->t('captcha', 'invalid_captcha_entered');

	if (isset($errors) === true) {
		ACP3_CMS::$view->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
	} else {
		$settings = ACP3_Config::getSettings('contact');

		$subject = sprintf(ACP3_CMS::$lang->t('contact', 'contact_subject'), CONFIG_SEO_TITLE);
		$body = str_replace(array('{name}', '{mail}', '{message}', '\n'), array($_POST['name'], $_POST['mail'], $_POST['message'], "\n"), ACP3_CMS::$lang->t('contact', 'contact_body'));
		$bool = generateEmail('', $settings['mail'], $_POST['mail'], $subject, $body);

		// Nachrichtenkopie an Absender senden
		if (isset($_POST['copy'])) {
			$subject2 = sprintf(ACP3_CMS::$lang->t('contact', 'sender_subject'), CONFIG_SEO_TITLE);
			$body2 = sprintf(ACP3_CMS::$lang->t('contact', 'sender_body'), CONFIG_SEO_TITLE, $_POST['message']);
			generateEmail($_POST['name'], $_POST['mail'], $settings['mail'], $subject2, $body2);
		}

		ACP3_CMS::$session->unsetFormToken();

		ACP3_CMS::setContent(confirmBox($bool === true ? ACP3_CMS::$lang->t('contact', 'send_mail_success') : ACP3_CMS::$lang->t('contact', 'send_mail_error'), ACP3_CMS::$uri->route('contact')));
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	// Falls Benutzer eingeloggt ist, Formular schon teilweise ausfüllen
	if (ACP3_CMS::$auth->isUser() === true) {
		$defaults = ACP3_CMS::$auth->getUserInfo();
		$disabled = ' readonly="readonly" class="readonly"';
		$defaults['name'] = !empty($defaults['realname']) ? $defaults['realname'] : $defaults['nickname'];
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
	ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? array_merge($defaults, $_POST) : $defaults);
	ACP3_CMS::$view->assign('copy_checked', selectEntry('copy', 1, 0, 'checked'));

	if ($captchaAccess === true) {
		require_once MODULES_DIR . 'captcha/functions.php';
		ACP3_CMS::$view->assign('captcha', captcha());
	}

	ACP3_CMS::$session->generateFormToken();

	ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('contact/list.tpl'));
}
