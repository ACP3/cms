<?php
/**
 * Users
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

if (!$auth->isUser() || !validate::isNumber($auth->getUserId())) {
	$uri->redirect('errors/403');
} else {
	breadcrumb::assign($lang->t('users', 'users'), $uri->route('users'));
	breadcrumb::assign($lang->t('users', 'home'), $uri->route('users/home'));
	breadcrumb::assign($lang->t('users', 'edit_profile'));

	if (isset($_POST['form']) === true) {
		require_once MODULES_DIR . 'users/functions.php';

		$form = $_POST['form'];

		if (empty($form['nickname']))
			$errors[] = $lang->t('common', 'name_to_short');
		if (userNameExists($form['nickname'], $auth->getUserId()))
			$errors[] = $lang->t('users', 'user_name_already_exists');
		if (!validate::gender($form['gender']))
			$errors[] = $lang->t('users', 'select_gender');
		if (!isset($form['birthday_format']) || !empty($form['birthday']) && !validate::birthday($form['birthday'], $form['birthday_format']))
			$errors[] = $lang->t('users', 'invalid_birthday');
		if (!validate::email($form['mail']))
			$errors[] = $lang->t('common', 'wrong_email_format');
		if (userEmailExists($form['mail'], $auth->getUserId()))
			$errors[] = $lang->t('users', 'user_email_already_exists');
		if (!empty($form['icq']) && !validate::icq($form['icq']))
			$errors[] = $lang->t('users', 'invalid_icq_number');
		if (!empty($form['msn']) && !validate::email($form['msn']))
			$errors[] = $lang->t('users', 'invalid_msn_account');
		if (!empty($form['new_pwd']) && !empty($form['new_pwd_repeat']) && $form['new_pwd'] != $form['new_pwd_repeat'])
			$errors[] = $lang->t('users', 'type_in_pwd');

		if (isset($errors) === true) {
			$tpl->assign('error_msg', errorBox($errors));
		} elseif (validate::formToken() === false) {
			view::setContent(errorBox($lang->t('common', 'form_already_submitted')));
		} else {
			$update_values = array(
				'nickname' => $db->escape($form['nickname']),
				'realname' => $db->escape($form['realname']) . ':' . (isset($form['realname_display']) ? '1' : '0'),
				'gender' => $form['gender'] . ':' . (isset($form['gender_display']) ? '1' : '0'),
				'birthday' => $date->timestamp($form['birthday']) . ':' . (isset($form['birthday_display']) ? '1' : '0'),
				'birthday_format' => $form['birthday_format'],
				'mail' => $form['mail'] . ':' . (isset($form['mail_display']) ? '1' : '0'),
				'website' => $db->escape($form['website'], 2) . ':' . (isset($form['website_display']) ? '1' : '0'),
				'icq' => $form['icq'] . ':' . (isset($form['icq_display']) ? '1' : '0'),
				'msn' => $db->escape($form['msn'], 2) . ':' . (isset($form['msn_display']) ? '1' : '0'),
				'skype' => $db->escape($form['skype']) . ':' . (isset($form['skype_display']) ? '1' : '0'),
			);

			// Neues Passwort
			if (!empty($form['new_pwd']) && !empty($form['new_pwd_repeat'])) {
				$salt = salt(12);
				$new_pwd = generateSaltedPassword($salt, $form['new_pwd']);
				$update_values['pwd'] = $new_pwd . ':' . $salt;
			}

			$bool = $db->update('users', $update_values, 'id = \'' . $auth->getUserId() . '\'');

			$cookie_arr = explode('|', base64_decode($_COOKIE['ACP3_AUTH']));
			$auth->setCookie($form['nickname'], isset($new_pwd) ? $new_pwd : $cookie_arr[1], 3600);

			$session->unsetFormToken();

			setRedirectMessage($bool !== false ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), 'users/home');
		}
	}
	if (isset($_POST['form']) === false || isset($errors) === true && is_array($errors) === true) {
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
		$contact[0]['value'] = isset($form) ? $form['mail'] : $user['mail'];
		$contact[0]['maxlength'] = '118';
		$contact[1]['name'] = 'website';
		$contact[1]['lang'] = $lang->t('common', 'website');
		$contact[1]['checked'] = selectEntry('website_display', 1, $user['website_display'], 'checked');
		$contact[1]['value'] = isset($form) ? $form['website'] : $db->escape($user['website'], 3);
		$contact[1]['maxlength'] = '118';
		$contact[2]['name'] = 'icq';
		$contact[2]['lang'] = $lang->t('users', 'icq');
		$contact[2]['checked'] = selectEntry('icq_display', 1, $user['icq_display'], 'checked');
		$contact[2]['value'] = isset($form) ? $form['icq'] : $user['icq'];
		$contact[2]['maxlength'] = '9';
		$contact[3]['name'] = 'msn';
		$contact[3]['lang'] = $lang->t('users', 'msn');
		$contact[3]['checked'] = selectEntry('msn_display', 1, $user['msn_display'], 'checked');
		$contact[3]['value'] = isset($form) ? $form['msn'] : $db->escape($user['msn'], 3);
		$contact[3]['maxlength'] = '118';
		$contact[4]['name'] = 'skype';
		$contact[4]['lang'] = $lang->t('users', 'skype');
		$contact[4]['checked'] = selectEntry('skype_display', 1, $user['skype_display'], 'checked');
		$contact[4]['value'] = isset($form) ? $form['skype'] : $db->escape($user['skype'], 3);
		$contact[4]['maxlength'] = '28';
		$tpl->assign('contact', $contact);

		$tpl->assign('form', isset($form) ? $form : $user);

		$session->generateFormToken();

		view::setContent(view::fetchTemplate('users/edit_profile.tpl'));
	}
}
