<?php
/**
 * Users
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

if ($auth->isUser() === false || ACP3_Validate::isNumber($auth->getUserId()) === false) {
	$uri->redirect('errors/403');
} else {
	$breadcrumb->append($lang->t('users', 'users'), $uri->route('users'))
			   ->append($lang->t('users', 'home'), $uri->route('users/home'))
			   ->append($lang->t('users', 'edit_profile'));

	if (isset($_POST['submit']) === true) {
		require_once MODULES_DIR . 'users/functions.php';

		if (empty($_POST['nickname']))
			$errors['nnickname'] = $lang->t('common', 'name_to_short');
		if (userNameExists($_POST['nickname'], $auth->getUserId()) === true)
			$errors['nickname'] = $lang->t('users', 'user_name_already_exists');
		if (ACP3_Validate::gender($_POST['gender']) === false)
			$errors['gender'] = $lang->t('users', 'select_gender');
		if (!isset($_POST['birthday_format']) || !empty($_POST['birthday']) && ACP3_Validate::birthday($_POST['birthday'], $_POST['birthday_format']) === false)
			$errors[] = $lang->t('users', 'invalid_birthday');
		if (ACP3_Validate::email($_POST['mail']) === false)
			$errors['mail'] = $lang->t('common', 'wrong_email_format');
		if (userEmailExists($_POST['mail'], $auth->getUserId()) === true)
			$errors['mail'] = $lang->t('users', 'user_email_already_exists');
		if (!empty($_POST['icq']) && ACP3_Validate::icq($_POST['icq']) === false)
			$errors['icq'] = $lang->t('users', 'invalid_icq_number');
		if (!empty($_POST['msn']) && ACP3_Validate::email($_POST['msn']) === false)
			$errors['msn'] = $lang->t('users', 'invalid_msn_account');
		if (!empty($_POST['new_pwd']) && !empty($_POST['new_pwd_repeat']) && $_POST['new_pwd'] != $_POST['new_pwd_repeat'])
			$errors[] = $lang->t('users', 'type_in_pwd');

		if (isset($errors) === true) {
			$tpl->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
		} else {
			$update_values = array(
				'nickname' => $db->escape($_POST['nickname']),
				'realname' => $db->escape($_POST['realname']) . ':' . (isset($_POST['realname_display']) ? '1' : '0'),
				'gender' => $_POST['gender'] . ':' . (isset($_POST['gender_display']) ? '1' : '0'),
				'birthday' => $date->timestamp($_POST['birthday']) . ':' . (isset($_POST['birthday_display']) ? '1' : '0'),
				'birthday_format' => $_POST['birthday_format'],
				'mail' => $_POST['mail'] . ':' . (isset($_POST['mail_display']) ? '1' : '0'),
				'website' => $db->escape($_POST['website'], 2) . ':' . (isset($_POST['website_display']) ? '1' : '0'),
				'icq' => $_POST['icq'] . ':' . (isset($_POST['icq_display']) ? '1' : '0'),
				'msn' => $db->escape($_POST['msn'], 2) . ':' . (isset($_POST['msn_display']) ? '1' : '0'),
				'skype' => $db->escape($_POST['skype']) . ':' . (isset($_POST['skype_display']) ? '1' : '0'),
			);

			// Neues Passwort
			if (!empty($_POST['new_pwd']) && !empty($_POST['new_pwd_repeat'])) {
				$salt = salt(12);
				$new_pwd = generateSaltedPassword($salt, $_POST['new_pwd']);
				$update_values['pwd'] = $new_pwd . ':' . $salt;
			}

			$bool = $db->update('users', $update_values, 'id = \'' . $auth->getUserId() . '\'');

			$cookie_arr = explode('|', base64_decode($_COOKIE['ACP3_AUTH']));
			$auth->setCookie($_POST['nickname'], isset($new_pwd) ? $new_pwd : $cookie_arr[1], 3600);

			$session->unsetFormToken();

			setRedirectMessage($bool !== false ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), 'users/home');
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$user = $auth->getUserInfo();
		$user['nickname'] = $db->escape($user['nickname'], 3);
		$user['realname'] = $db->escape($user['realname'], 3);

		$checked = array();
		$checked['realname'] = selectEntry('realname_display', 1, $user['realname_display'], 'checked');
		$checked['gender'] = selectEntry('gender_display', 1, $user['gender_display'], 'checked');
		$checked['birthday'] = selectEntry('birthday_display', 1, $user['birthday_display'], 'checked');
		$tpl->assign('checked', $checked);

		// Geschlecht
		$gender = array();
		$gender[0]['value'] = '1';
		$gender[0]['selected'] = selectEntry('gender', 1, $user['gender']);
		$gender[0]['lang'] = '-';
		$gender[1]['value'] = '2';
		$gender[1]['selected'] = selectEntry('gender', 2, $user['gender']);
		$gender[1]['lang'] = $lang->t('users', 'female');
		$gender[2]['value'] = '3';
		$gender[2]['selected'] = selectEntry('gender', 3, $user['gender']);
		$gender[2]['lang'] = $lang->t('users', 'male');
		$tpl->assign('gender', $gender);

		// Geburtstag
		$tpl->assign('birthday_datepicker', $date->datepicker('birthday', $user['birthday'], 'Y-m-d', array('constrainInput' => 'true', 'changeMonth' => 'true', 'changeYear' => 'true', 'yearRange' => '\'-50:+0\''), 0));
		$birthday_format = array();
		$birthday_format[0]['name'] = 'full';
		$birthday_format[0]['value'] = '1';
		$birthday_format[0]['checked'] = selectEntry('birthday_format', '1', $user['birthday_format'], 'checked');
		$birthday_format[0]['lang'] = $lang->t('users', 'birthday_display_completely');
		$birthday_format[1]['name'] = 'hide_year';
		$birthday_format[1]['value'] = '2';
		$birthday_format[1]['checked'] = selectEntry('birthday_format', '2', $user['birthday_format'], 'checked');
		$birthday_format[1]['lang'] = $lang->t('users', 'birthday_hide_year');
		$tpl->assign('birthday_format', $birthday_format);

		// Kontaktangaben
		$contact = array();
		$contact[0]['name'] = 'mail';
		$contact[0]['lang'] = $lang->t('common', 'email');
		$contact[0]['checked'] = selectEntry('mail_display', 1, $user['mail_display'], 'checked');
		$contact[0]['value'] = isset($_POST['submit']) ? $_POST['mail'] : $user['mail'];
		$contact[0]['maxlength'] = '118';
		$contact[1]['name'] = 'website';
		$contact[1]['lang'] = $lang->t('common', 'website');
		$contact[1]['checked'] = selectEntry('website_display', 1, $user['website_display'], 'checked');
		$contact[1]['value'] = isset($_POST['submit']) ? $_POST['website'] : $db->escape($user['website'], 3);
		$contact[1]['maxlength'] = '118';
		$contact[2]['name'] = 'icq';
		$contact[2]['lang'] = $lang->t('users', 'icq');
		$contact[2]['checked'] = selectEntry('icq_display', 1, $user['icq_display'], 'checked');
		$contact[2]['value'] = isset($_POST['submit']) ? $_POST['icq'] : $user['icq'];
		$contact[2]['maxlength'] = '9';
		$contact[3]['name'] = 'msn';
		$contact[3]['lang'] = $lang->t('users', 'msn');
		$contact[3]['checked'] = selectEntry('msn_display', 1, $user['msn_display'], 'checked');
		$contact[3]['value'] = isset($_POST['submit']) ? $_POST['msn'] : $db->escape($user['msn'], 3);
		$contact[3]['maxlength'] = '118';
		$contact[4]['name'] = 'skype';
		$contact[4]['lang'] = $lang->t('users', 'skype');
		$contact[4]['checked'] = selectEntry('skype_display', 1, $user['skype_display'], 'checked');
		$contact[4]['value'] = isset($_POST['submit']) ? $_POST['skype'] : $db->escape($user['skype'], 3);
		$contact[4]['maxlength'] = '28';
		$tpl->assign('contact', $contact);

		$tpl->assign('form', isset($_POST['submit']) ? $_POST : $user);

		$session->generateFormToken();

		ACP3_View::setContent(ACP3_View::fetchTemplate('users/edit_profile.tpl'));
	}
}
