<?php
if (defined('IN_ACP3') === false)
	exit;

$settings = ACP3_Config::getSettings('users');

if (ACP3_CMS::$auth->isUser() === true) {
	ACP3_CMS::$uri->redirect(0, ROOT_DIR);
} elseif ($settings['enable_registration'] == 0) {
	ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('users', 'user_registration_disabled')));
} else {
	ACP3_CMS::$breadcrumb
	->append(ACP3_CMS::$lang->t('users', 'users'), ACP3_CMS::$uri->route('users'))
	->append(ACP3_CMS::$lang->t('users', 'register'));

	$captchaAccess = ACP3_Modules::check('captcha', 'functions');

	if (isset($_POST['submit']) === true) {
		require_once MODULES_DIR . 'users/functions.php';

		if (empty($_POST['nickname']))
			$errors['nickname'] = ACP3_CMS::$lang->t('system', 'name_to_short');
		if (userNameExists($_POST['nickname']) === true)
			$errors['nickname'] = ACP3_CMS::$lang->t('users', 'user_name_already_exists');
		if (ACP3_Validate::email($_POST['mail']) === false)
			$errors['mail'] = ACP3_CMS::$lang->t('system', 'wrong_email_format');
		if (userEmailExists($_POST['mail']) === true)
			$errors['mail'] = ACP3_CMS::$lang->t('users', 'user_email_already_exists');
		if (empty($_POST['pwd']) || empty($_POST['pwd_repeat']) || $_POST['pwd'] != $_POST['pwd_repeat'])
			$errors[] = ACP3_CMS::$lang->t('users', 'type_in_pwd');
		if ($captchaAccess === true && ACP3_Validate::captcha($_POST['captcha']) === false)
			$errors['captcha'] = ACP3_CMS::$lang->t('captcha', 'invalid_captcha_entered');

		if (isset($errors) === true) {
			ACP3_CMS::$view->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
		} else {
			// E-Mail mit den Accountdaten zusenden
			$host = htmlentities($_SERVER['HTTP_HOST']);
			$subject = str_replace(array('{title}', '{host}'), array(CONFIG_SEO_TITLE, $host), ACP3_CMS::$lang->t('users', 'register_mail_subject'));
			$body = str_replace(array('{name}', '{mail}', '{password}', '{title}', '{host}'), array($_POST['nickname'], $_POST['mail'], $_POST['pwd'], CONFIG_SEO_TITLE, $host), ACP3_CMS::$lang->t('users', 'register_mail_message'));
			$mail_sent = generateEmail('', $_POST['mail'], $subject, $body);

			$salt = salt(12);
			$insert_values = array(
				'nickname' => str_encode($_POST['nickname']),
				'pwd' => generateSaltedPassword($salt, $_POST['pwd']) . ':' . $salt,
				'birthday_display' => '1',
				'mail' => $_POST['mail'],
				'date_format_long' => CONFIG_DATE_FORMAT_LONG,
				'date_format_short' => CONFIG_DATE_FORMAT_SHORT,
				'time_zone' => CONFIG_DATE_TIME_ZONE,
				'language' => CONFIG_LANG,
				'entries' => CONFIG_ENTRIES,
			);

			ACP3_CMS::$db2->beginTransaction();
			try {
				$bool = ACP3_CMS::$db2->insert(DB_PRE . 'users', $insert_values);
				$user_id = ACP3_CMS::$db2->lastInsertId();
				$bool2 = ACP3_CMS::$db2->insert(DB_PRE . 'acl_user_roles', array('user_id' => $user_id, 'role_id' => 2));
				ACP3_CMS::$db2->commit();
			} catch(Exception $e) {
				ACP3_CMS::$db2->rollback();
				$bool = $bool2 = false;
			}

			ACP3_CMS::$session->unsetFormToken();

			ACP3_CMS::setContent(confirmBox(ACP3_CMS::$lang->t('users', $mail_sent === true && $bool !== false && $bool2 !== false ? 'register_success' : 'register_error'), ROOT_DIR));
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$defaults = array(
			'nickname' => '',
			'mail' => '',
		);

		ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : $defaults);

		if ($captchaAccess === true) {
			require_once MODULES_DIR . 'captcha/functions.php';
			ACP3_CMS::$view->assign('captcha', captcha());
		}

		ACP3_CMS::$session->generateFormToken();

		ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('users/register.tpl'));
	}
}