<?php
if (defined('IN_ACP3') === false)
	exit;

if ($auth->isUser()) {
	$uri->redirect(0, ROOT_DIR);
} else {
	breadcrumb::assign($lang->t('users', 'users'), $uri->route('users'));
	breadcrumb::assign($lang->t('users', 'register'));

	if (isset($_POST['form'])) {
		require_once MODULES_DIR . 'users/functions.php';

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
		if (!$auth->isUser() && !validate::captcha($form['captcha'], $form['hash']))
			$errors[] = $lang->t('captcha', 'invalid_captcha_entered');

		if (isset($errors)) {
			$tpl->assign('error_msg', comboBox($errors));
		} else {
			// E-Mail mit den Accountdaten zusenden
			$form['nickname'] = $db->escape($form['nickname']);
			$host = htmlentities($_SERVER['HTTP_HOST']);
			$subject = str_replace(array('{title}', '{host}'), array(CONFIG_SEO_TITLE, $host), $lang->t('users', 'register_mail_subject'));
			$body = str_replace(array('{name}', '{mail}', '{password}', '{title}', '{host}'), array($form['nickname'], $form['mail'], $form['pwd'], CONFIG_SEO_TITLE, $host), $lang->t('users', 'register_mail_message'));
			$mail_sent = genEmail('', $form['mail'], $subject, $body);

			// Das Benutzerkonto nur erstellen, wenn die E-Mail erfolgreich versendet werden konnte
			$salt = salt(12);
			$insert_values = array(
				'id' => '',
				'nickname' => $form['nickname'],
				'pwd' => genSaltedPassword($salt, $form['pwd']) . ':' . $salt,
				'realname' => ':1',
				'gender' => ':1',
				'birthday' => ':1',
				'birthday_format' => '1',
				'mail' => $form['mail'] . ':1',
				'website' => ':1',
				'icq' => ':1',
				'msn' => ':1',
				'skype' => ':1',
				'date_format_long' => CONFIG_DATE_FORMAT_LONG,
				'date_format_short' => CONFIG_DATE_FORMAT_SHORT,
				'time_zone' => CONFIG_DATE_TIME_ZONE,
				'dst' => CONFIG_DATE_DST,
				'language' => CONFIG_LANG,
				'draft' => '',
			);

			$db->link->beginTransaction();
			$bool = $db->insert('users', $insert_values);
			$user_id = $db->link->lastInsertId();
			$bool2 = $db->insert('acl_user_roles', array('user_id' => $user_id, 'role_id' => 2));
			$db->link->commit();

			$content = comboBox($mail_sent && $bool && $bool2 ? $lang->t('users', 'register_success') : $lang->t('users', 'register_error'), ROOT_DIR);
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