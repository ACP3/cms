<?php
if (!defined('IN_ACP3'))
	exit;

if ($auth->isUser()) {
	redirect(0, ROOT_DIR);
} else {
	breadcrumb::assign($lang->t('users', 'users'), uri('users'));
	breadcrumb::assign($lang->t('users', 'register'));

	if (isset($_POST['form'])) {
		require_once ACP3_ROOT . 'modules/users/functions.php';

		$form = $_POST['form'];

		if (empty($form['nickname']))
			$errors[] = $lang->t('common', 'name_to_short');
		if (!userNameExists($form['nickname']))
			$errors[] = $lang->t('users', 'user_name_already_exists');
		if (!validate::email($form['mail']))
			$errors[] = $lang->t('common', 'wrong_email_format');
		if (!userEmailExists($form['mail']))
			$errors[] = $lang->t('users', 'user_email_already_exists');
		if (empty($form['pwd']) || empty($form['pwd_repeat']) || $form['pwd'] != $form['pwd_repeat'])
			$errors[] = $lang->t('users', 'type_in_pwd');
		if (!validate::captcha($form['captcha'], $form['hash']))
			$errors[] = $lang->t('captcha', 'invalid_captcha_entered');

		if (isset($errors)) {
			$tpl->assign('error_msg', comboBox($errors));
		} else {
			// E-Mail mit den Accountdaten zusenden
			$form['nickname'] = db::escape($form['nickname']);
			$host = htmlentities($_SERVER['HTTP_HOST']);
			$subject = str_replace(array('{title}', '{host}'), array(CONFIG_SEO_TITLE, $host), $lang->t('users', 'register_mail_subject'));
			$body = str_replace(array('{name}', '{mail}', '{password}', '{title}', '{host}'), array($form['nickname'], $form['mail'], $form['pwd'], CONFIG_SEO_TITLE, $host), $lang->t('users', 'register_mail_message'));
			$mail_sent = genEmail('', $form['mail'], $subject, $body);

			// Das Benutzerkonto nur erstellen, wenn die E-Mail erfolgreich versendet werden konnte
			if ($mail_sent) {
				$salt = salt(12);
				$insert_values = array(
					'id' => '',
					'nickname' => $form['nickname'],
					'realname' => '',
					'pwd' => genSaltedPassword($salt, $form['pwd']) . ':' . $salt,
					'access' => '3',
					'mail' => $form['mail'],
					'website' => '',
					'time_zone' => CONFIG_DATE_TIME_ZONE,
					'dst' => CONFIG_DATE_DST,
					'language' => CONFIG_LANG,
					'draft' => '',
				);

				$bool = $db->insert('users', $insert_values);
			}

			$content = comboBox($mail_sent && isset($bool) && $bool ? $lang->t('users', 'register_success') : $lang->t('users', 'register_error'), ROOT_DIR);
		}
	}
	if (!isset($_POST['form']) || isset($errors) && is_array($errors)) {
		$defaults = array(
			'nickname' => '',
			'mail' => '',
		);

		$tpl->assign('form', isset($form) ? $form : $defaults);

		$tpl->assign('captcha', captcha());

		$content = modules::fetchTemplate('users/register.html');
	}
}