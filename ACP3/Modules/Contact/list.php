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

$captchaAccess = ACP3\Core\Modules::check('captcha', 'functions');

if (isset($_POST['submit']) === true) {
	if (empty($_POST['name']))
		$errors['name'] = ACP3\CMS::$injector['Lang']->t('system', 'name_to_short');
	if (ACP3\Core\Validate::email($_POST['mail']) === false)
		$errors['mail'] = ACP3\CMS::$injector['Lang']->t('system', 'wrong_email_format');
	if (strlen($_POST['message']) < 3)
		$errors['message'] = ACP3\CMS::$injector['Lang']->t('system', 'message_to_short');
	if ($captchaAccess === true && ACP3\CMS::$injector['Auth']->isUser() === false && ACP3\Core\Validate::captcha($_POST['captcha']) === false)
		$errors['captcha'] = ACP3\CMS::$injector['Lang']->t('captcha', 'invalid_captcha_entered');

	if (isset($errors) === true) {
		ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
	} elseif (ACP3\Core\Validate::formToken() === false) {
		ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted')));
	} else {
		$settings = ACP3\Core\Config::getSettings('contact');
		$_POST['message'] = ACP3\Core\Functions::str_encode($_POST['message'], true);

		$subject = sprintf(ACP3\CMS::$injector['Lang']->t('contact', 'contact_subject'), CONFIG_SEO_TITLE);
		$body = str_replace(array('{name}', '{mail}', '{message}', '\n'), array($_POST['name'], $_POST['mail'], $_POST['message'], "\n"), ACP3\CMS::$injector['Lang']->t('contact', 'contact_body'));
		$bool = generateEmail('', $settings['mail'], $_POST['mail'], $subject, $body);

		// Nachrichtenkopie an Absender senden
		if (isset($_POST['copy'])) {
			$subject2 = sprintf(ACP3\CMS::$injector['Lang']->t('contact', 'sender_subject'), CONFIG_SEO_TITLE);
			$body2 = sprintf(ACP3\CMS::$injector['Lang']->t('contact', 'sender_body'), CONFIG_SEO_TITLE, $_POST['message']);
			generateEmail($_POST['name'], $_POST['mail'], $settings['mail'], $subject2, $body2);
		}

		ACP3\CMS::$injector['Session']->unsetFormToken();

		ACP3\CMS::$injector['View']->setContent(confirmBox($bool === true ? ACP3\CMS::$injector['Lang']->t('contact', 'send_mail_success') : ACP3\CMS::$injector['Lang']->t('contact', 'send_mail_error'), ACP3\CMS::$injector['URI']->route('contact')));
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	// Falls Benutzer eingeloggt ist, Formular schon teilweise ausfüllen
	if (ACP3\CMS::$injector['Auth']->isUser() === true) {
		$defaults = ACP3\CMS::$injector['Auth']->getUserInfo();
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
	ACP3\CMS::$injector['View']->assign('form', isset($_POST['submit']) ? array_merge($defaults, $_POST) : $defaults);
	ACP3\CMS::$injector['View']->assign('copy_checked', ACP3\Core\Functions::selectEntry('copy', 1, 0, 'checked'));

	if ($captchaAccess === true) {
		require_once MODULES_DIR . 'captcha/functions.php';
		ACP3\CMS::$injector['View']->assign('captcha', captcha());
	}

	ACP3\CMS::$injector['Session']->generateFormToken();
}
