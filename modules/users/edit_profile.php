<?php
/**
 * Users
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;

if (!$auth->isUser() || !validate::isNumber(USER_ID)) {
	redirect('errors/403');
} else {
	breadcrumb::assign($lang->t('users', 'users'), uri('users'));
	breadcrumb::assign($lang->t('users', 'home'), uri('users/home'));
	breadcrumb::assign($lang->t('users', 'edit_profile'));

	if (isset($_POST['submit'])) {
		$form = $_POST['form'];

		if (empty($form['nickname']))
			$errors[] = $lang->t('common', 'name_to_short');
		if (userNameExists($form['nickname'], USER_ID))
			$errors[] = $lang->t('users', 'user_name_already_exists');
		if (!validate::email($form['mail']))
			$errors[] = $lang->t('common', 'wrong_email_format');
		if (userEmailExists($form['mail'], USER_ID))
			$errors[] = $lang->t('users', 'user_email_already_exists');
		if (!empty($form['new_pwd']) && !empty($form['new_pwd_repeat']) && $form['new_pwd'] != $form['new_pwd_repeat'])
			$errors[] = $lang->t('users', 'type_in_pwd');

		if (isset($errors)) {
			$tpl->assign('error_msg', comboBox($errors));
		} else {
			$new_pwd_sql = null;
			// Neues Passwort
			if (!empty($form['new_pwd']) && !empty($form['new_pwd_repeat'])) {
				$salt = salt(12);
				$new_pwd = sha1($salt . sha1($form['new_pwd']));
				$new_pwd_sql = array('pwd' => $new_pwd . ':' . $salt);
			}

			$update_values = array(
				'nickname' => $db->escape($form['nickname']),
				'realname' => $db->escape($form['realname']) . ':' . (isset($form['realname_display']) ? '1' : '0'),
				'mail' => $form['mail'] . ':' . (isset($form['mail_display']) ? '1' : '0'),
				'website' => $db->escape($form['website'], 2) . ':' . (isset($form['website_display']) ? '1' : '0'),
			);
			if (is_array($new_pwd_sql)) {
				$update_values = array_merge($update_values, $new_pwd_sql);
			}

			$bool = $db->update('users', $update_values, 'id = \'' . USER_ID . '\'');

			$cookie_arr = explode('|', base64_decode($_COOKIE['ACP3_AUTH']));
			$cookie_value = base64_encode($form['nickname'] . '|' . (isset($new_pwd) ? $new_pwd : $cookie_arr[1]));
			setcookie('ACP3_AUTH', $cookie_value, time() + 3600, '/');

			$content = comboBox($bool !== null ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), uri('users/home'));
		}
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$user = $auth->getUserInfo();

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
		$tpl->assign('birthday_datepicker', datepicker('birthday', $user['birthday']));
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
		$contact[1]['value'] = isset($form) ? $form['website'] : $user['website'];
		$contact[1]['maxlength'] = '118';
		$contact[2]['name'] = 'icq';
		$contact[2]['lang'] = $lang->t('users', 'icq');
		$contact[2]['checked'] = selectEntry('icq_display', 1, $user['icq_display'], 'checked');
		$contact[2]['value'] = isset($form) ? $form['icq'] : $user['icq'];
		$contact[2]['maxlength'] = '9';
		$contact[3]['name'] = 'msn';
		$contact[3]['lang'] = $lang->t('users', 'msn');
		$contact[3]['checked'] = selectEntry('msn_display', 1, $user['msn_display'], 'checked');
		$contact[3]['value'] = isset($form) ? $form['msn'] : $user['msn'];
		$contact[3]['maxlength'] = '118';
		$contact[4]['name'] = 'skype';
		$contact[4]['lang'] = $lang->t('users', 'skype');
		$contact[4]['checked'] = selectEntry('skype_display', 1, $user['skype_display'], 'checked');
		$contact[4]['value'] = isset($form) ? $form['skype'] : $user['skype'];
		$contact[4]['maxlength'] = '28';
		$tpl->assign('contact', $contact);

		$tpl->assign('form', isset($form) ? $form : $user);

		$content = $tpl->fetch('users/edit_profile.html');
	}
}
?>