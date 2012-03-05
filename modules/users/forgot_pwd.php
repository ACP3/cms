<?php
if (defined('IN_ACP3') === false)
	exit;

if ($auth->isUser() === true) {
	$uri->redirect(0, ROOT_DIR);
} else {
	$breadcrumb->append($lang->t('users', 'users'), $uri->route('users'))
			   ->append($lang->t('users', 'forgot_pwd'));

	if (isset($_POST['submit']) === true) {
		require_once MODULES_DIR . 'users/functions.php';

		if (empty($_POST['nick_mail']))
			$errors['nick-mail'] = $lang->t('users', 'type_in_nickname_or_email');
		if (!empty($_POST['nick_mail']) && ACP3_Validate::email($_POST['nick_mail']) === false && userNameExists($_POST['nick_mail']) === false)
			$errors['nick-mail'] = $lang->t('users', 'user_not_exists');
		if (!empty($_POST['nick_mail']) && ACP3_Validate::email($_POST['nick_mail']) === false)
			$errors['nick-mail'] = $lang->t('common', 'wrong_email_format');
		if (ACP3_Validate::email($_POST['nick_mail']) && userEmailExists($_POST['nick_mail']) === false)
			$errors['nick-mail'] = $lang->t('users', 'user_not_exists');
		if ($auth->isUser() === false && ACP3_Validate::captcha($_POST['captcha']) === false)
			$errors['captcha'] = $lang->t('captcha', 'invalid_captcha_entered');

		if (isset($errors) === true) {
			$tpl->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
		} else {
			// Neues Passwort und neuen Zufallsschlüssel erstellen
			$new_password = salt(8);
			$host = htmlentities($_SERVER['HTTP_HOST']);

			// Je nachdem welches Feld ausgefüllt wurde, dieses auswählen
			$where = ACP3_Validate::email($_POST['nick_mail']) && userEmailExists($_POST['nick_mail']) === true ? 'mail = \'' . $_POST['nick_mail'] . '\'' : 'nickname = \'' . $db->escape($_POST['nick_mail']) . '\'';
			$user = $db->select('id, nickname, mail', 'users', $where);

			// E-Mail mit dem neuen Passwort versenden
			$subject = str_replace(array('{title}', '{host}'), array(CONFIG_SEO_TITLE, $host), $lang->t('users', 'forgot_pwd_mail_subject'));
			$body = str_replace(array('{name}', '{mail}', '{password}', '{title}', '{host}'), array($db->escape($user[0]['nickname'], 3), $user[0]['mail'], $new_password, CONFIG_SEO_TITLE, $host), $lang->t('users', 'forgot_pwd_mail_message'));
			$mail_sent = generateEmail('', $user[0]['mail'], $subject, $body);

			// Das Passwort des Benutzers nur abändern, wenn die E-Mail erfolgreich versendet werden konnte
			if ($mail_sent === true) {
				$salt = salt(12);
				$bool = $db->update('users', array('pwd' => generateSaltedPassword($salt, $new_password) . ':' . $salt, 'login_errors' => 0), 'id = \'' . $user[0]['id'] . '\'');
			}

			$session->unsetFormToken();

			ACP3_View::setContent(confirmBox($mail_sent === true && isset($bool) && $bool !== false ? $lang->t('users', 'forgot_pwd_success') : $lang->t('users', 'forgot_pwd_error'), ROOT_DIR));
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$defaults = array('nick_mail' => '');

		$tpl->assign('form', isset($_POST['submit']) ? $_POST : $defaults);

		$tpl->assign('captcha', captcha());

		$session->generateFormToken();

		ACP3_View::setContent(ACP3_View::fetchTemplate('users/forgot_pwd.tpl'));
	}
}