<?php
if (!defined('IN_ACP3'))
	exit;

if ($auth->isUser()) {
	redirect(0, ROOT_DIR);
} else {
	breadcrumb::assign($lang->t('users', 'users'), uri('users'));
	breadcrumb::assign($lang->t('users', 'forgot_pwd'));

	if (isset($_POST['form'])) {
		require_once ACP3_ROOT . 'modules/users/functions.php';

		$form = $_POST['form'];

		if (empty($form['nick_mail']))
			$errors[] = $lang->t('users', 'type_in_nickname_or_email');
		if (!empty($form['nick_mail']) && !validate::email($form['nick_mail']) && !userNameExists($form['nick_mail']))
			$errors[] = $lang->t('users', 'user_not_exists');
		if (!empty($form['nick_mail']) && !validate::email($form['nick_mail']))
			$errors[] = $lang->t('common', 'wrong_email_format');
		if (validate::email($form['nick_mail']) && !userEmailExists($form['nick_mail']))
			$errors[] = $lang->t('users', 'user_not_exists');
		if (!validate::captcha($form['captcha'], $form['hash']))
			$errors[] = $lang->t('captcha', 'invalid_captcha_entered');

		if (isset($errors)) {
			$tpl->assign('error_msg', comboBox($errors));
		} else {
			// Neues Passwort und neuen Zufallsschlüssel erstellen
			$new_password = salt(8);
			$host = htmlentities($_SERVER['HTTP_HOST']);

			// Je nachdem welches Feld ausgefüllt wurde, dieses auswählen
			$where = validate::email($form['nick_mail']) && userEmailExists($form['nick_mail']) ? 'mail = \'' . $form['nick_mail'] . '\'' : 'nickname = \'' . db::escape($form['nick_mail']) . '\'';
			$user = $db->select('id, nickname, mail', 'users', $where);

			// E-Mail mit dem neuen Passwort versenden
			$subject = str_replace(array('{title}', '{host}'), array(CONFIG_SEO_TITLE, $host), $lang->t('users', 'forgot_pwd_mail_subject'));
			$body = str_replace(array('{name}', '{mail}', '{password}', '{title}', '{host}'), array($user[0]['nickname'], $user[0]['mail'], $new_password, CONFIG_SEO_TITLE, $host), $lang->t('users', 'forgot_pwd_mail_message'));
			$mail_sent = genEmail('', $user[0]['mail'], $subject, $body);

			// Das Passwort des Benutzers nur abändern, wenn die E-Mail erfolgreich versendet werden konnte
			if ($mail_sent) {
				$salt = salt(12);
				$bool = $db->update('users', array('pwd' => genSaltedPassword($salt, $new_password) . ':' . $salt, 'login_errors' => 0), 'id = \'' . $user[0]['id'] . '\'');
			}
			$content = comboBox($mail_sent && isset($bool) && $bool !== null ? $lang->t('users', 'forgot_pwd_success') : $lang->t('users', 'forgot_pwd_error'), ROOT_DIR);
		}
	}
	if (!isset($_POST['form']) || isset($errors) && is_array($errors)) {
		$defaults = array('nick_mail' => '');

		$tpl->assign('form', isset($form) ? $form : $defaults);

		$tpl->assign('captcha', captcha());

		$content = modules::fetchTemplate('users/forgot_pwd.html');
	}
}