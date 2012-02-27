<?php
if (defined('IN_ACP3') === false)
	exit;

$settings = config::getModuleSettings('users');

if ($auth->isUser() === true) {
	$uri->redirect(0, ROOT_DIR);
} elseif ($settings['enable_registration'] == 0) {
	view::setContent(errorBox($lang->t('users', 'user_registration_disabled')));
} else {
	$breadcrumb->append($lang->t('users', 'users'), $uri->route('users'))
			   ->append($lang->t('users', 'register'));

	if (isset($_POST['form']) === true) {
		require_once MODULES_DIR . 'users/functions.php';

		$form = $_POST['form'];

		if (empty($form['nickname']))
			$errors['nickname'] = $lang->t('common', 'name_to_short');
		if (userNameExists($form['nickname']) === true)
			$errors['nickname'] = $lang->t('users', 'user_name_already_exists');
		if (validate::email($form['mail']) === false)
			$errors['mail'] = $lang->t('common', 'wrong_email_format');
		if (userEmailExists($form['mail']) === true)
			$errors['mail'] = $lang->t('users', 'user_email_already_exists');
		if (empty($form['pwd']) || empty($form['pwd_repeat']) || $form['pwd'] != $form['pwd_repeat'])
			$errors[] = $lang->t('users', 'type_in_pwd');
		if ($auth->isUser() === false && validate::captcha($form['captcha']) === false)
			$errors['captcha'] = $lang->t('captcha', 'invalid_captcha_entered');

		if (isset($errors) === true) {
			$tpl->assign('error_msg', errorBox($errors));
		} elseif (validate::formToken() === false) {
			view::setContent(errorBox($lang->t('common', 'form_already_submitted')));
		} else {
			// E-Mail mit den Accountdaten zusenden
			$form['nickname'] = $db->escape($form['nickname']);
			$host = htmlentities($_SERVER['HTTP_HOST']);
			$subject = str_replace(array('{title}', '{host}'), array(CONFIG_SEO_TITLE, $host), $lang->t('users', 'register_mail_subject'));
			$body = str_replace(array('{name}', '{mail}', '{password}', '{title}', '{host}'), array($form['nickname'], $form['mail'], $form['pwd'], CONFIG_SEO_TITLE, $host), $lang->t('users', 'register_mail_message'));
			$mail_sent = generateEmail('', $form['mail'], $subject, $body);

			$salt = salt(12);
			$insert_values = array(
				'id' => '',
				'nickname' => $form['nickname'],
				'pwd' => generateSaltedPassword($salt, $form['pwd']) . ':' . $salt,
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
				'entries' => CONFIG_ENTRIES,
				'draft' => '',
			);

			$db->link->beginTransaction();
			$bool = $db->insert('users', $insert_values);
			$user_id = $db->link->lastInsertId();
			$bool2 = $db->insert('acl_user_roles', array('user_id' => $user_id, 'role_id' => 2));
			$db->link->commit();

			$session->unsetFormToken();

			view::setContent(confirmBox($mail_sent === true && $bool !== false && $bool2 !== false ? $lang->t('users', 'register_success') : $lang->t('users', 'register_error'), ROOT_DIR));
		}
	}
	if (isset($_POST['form']) === false || isset($errors) === true && is_array($errors) === true) {
		$defaults = array(
			'nickname' => '',
			'mail' => '',
		);

		$tpl->assign('form', isset($form) ? $form : $defaults);

		$tpl->assign('captcha', captcha());

		$session->generateFormToken();

		view::setContent(view::fetchTemplate('users/register.tpl'));
	}
}