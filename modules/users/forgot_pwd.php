<?php
if (defined('IN_ACP3') === false)
	exit;

if (ACP3_CMS::$auth->isUser() === true) {
	ACP3_CMS::$uri->redirect(0, ROOT_DIR);
} else {
	ACP3_CMS::$breadcrumb
	->append(ACP3_CMS::$lang->t('users', 'users'), ACP3_CMS::$uri->route('users'))
	->append(ACP3_CMS::$lang->t('users', 'forgot_pwd'));

	$captchaAccess = ACP3_Modules::check('captcha', 'functions');

	if (isset($_POST['submit']) === true) {
		require_once MODULES_DIR . 'users/functions.php';

		if (empty($_POST['nick_mail']))
			$errors['nick-mail'] = ACP3_CMS::$lang->t('users', 'type_in_nickname_or_email');
		elseif (ACP3_Validate::email($_POST['nick_mail']) === false && userNameExists($_POST['nick_mail']) === false)
			$errors['nick-mail'] = ACP3_CMS::$lang->t('users', 'user_not_exists');
		elseif (ACP3_Validate::email($_POST['nick_mail']) === true && userEmailExists($_POST['nick_mail']) === false)
			$errors['nick-mail'] = ACP3_CMS::$lang->t('users', 'user_not_exists');
		if ($captchaAccess === true && ACP3_Validate::captcha($_POST['captcha']) === false)
			$errors['captcha'] = ACP3_CMS::$lang->t('captcha', 'invalid_captcha_entered');

		if (isset($errors) === true) {
			ACP3_CMS::$view->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
		} else {
			// Neues Passwort und neuen Zufallsschlüssel erstellen
			$new_password = salt(8);
			$host = htmlentities($_SERVER['HTTP_HOST']);

			// Je nachdem, wie das Feld ausgefüllt wurde, dieses auswählen
			if (ACP3_Validate::email($_POST['nick_mail']) === true && userEmailExists($_POST['nick_mail']) === true) {
				$query = 'SELECT id, nickname, realname, mail FROM ' . DB_PRE . 'users WHERE mail = ?';
			} else {
				$query = 'SELECT id, nickname, realname, mail FROM ' . DB_PRE . 'users WHERE nickname = ?';
			}
			$user = ACP3_CMS::$db2->fetchAssoc($query, array($_POST['nick_mail']));

			// E-Mail mit dem neuen Passwort versenden
			$subject = str_replace(array('{title}', '{host}'), array(CONFIG_SEO_TITLE, $host), ACP3_CMS::$lang->t('users', 'forgot_pwd_mail_subject'));
			$search = array('{name}', '{mail}', '{password}', '{title}', '{host}');
			$replace = array($user['nickname'], $user['mail'], $new_password, CONFIG_SEO_TITLE, $host);
			$body = str_replace($search, $replace, ACP3_CMS::$lang->t('users', 'forgot_pwd_mail_message'));

			$settings = ACP3_Config::getSettings('users');
			$mail_sent = generateEmail(substr($user['realname'], 0, -2), $user['mail'], $settings['mail'], $subject, $body);

			// Das Passwort des Benutzers nur abändern, wenn die E-Mail erfolgreich versendet werden konnte
			if ($mail_sent === true) {
				$salt = salt(12);
				$bool = ACP3_CMS::$db2->update(DB_PRE . 'users', array('pwd' => generateSaltedPassword($salt, $new_password) . ':' . $salt, 'login_errors' => 0), array('id' => $user['id']));
			}

			ACP3_CMS::$session->unsetFormToken();

			ACP3_CMS::setContent(confirmBox(ACP3_CMS::$lang->t('users', $mail_sent === true && isset($bool) && $bool !== false ? 'forgot_pwd_success'  : 'forgot_pwd_error'), ROOT_DIR));
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$defaults = array('nick_mail' => '');

		ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : $defaults);

		if ($captchaAccess === true) {
			require_once MODULES_DIR . 'captcha/functions.php';
			ACP3_CMS::$view->assign('captcha', captcha());
		}

		ACP3_CMS::$session->generateFormToken();
	}
}