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

if (ACP3_CMS::$auth->isUser() === false || ACP3_Validate::isNumber(ACP3_CMS::$auth->getUserId()) === false) {
	ACP3_CMS::$uri->redirect('errors/403');
} else {
	ACP3_CMS::$breadcrumb
	->append(ACP3_CMS::$lang->t('users', 'users'), ACP3_CMS::$uri->route('users'))
	->append(ACP3_CMS::$lang->t('users', 'home'), ACP3_CMS::$uri->route('users/home'))
	->append(ACP3_CMS::$lang->t('users', 'edit_profile'));

	if (isset($_POST['submit']) === true) {
		require_once MODULES_DIR . 'users/functions.php';

		if (empty($_POST['nickname']))
			$errors['nnickname'] = ACP3_CMS::$lang->t('system', 'name_to_short');
		if (userNameExists($_POST['nickname'], ACP3_CMS::$auth->getUserId()) === true)
			$errors['nickname'] = ACP3_CMS::$lang->t('users', 'user_name_already_exists');
		if (ACP3_Validate::gender($_POST['gender']) === false)
			$errors['gender'] = ACP3_CMS::$lang->t('users', 'select_gender');
		if (!isset($_POST['birthday_display']) || !empty($_POST['birthday']) && ACP3_Validate::birthday($_POST['birthday'], $_POST['birthday_display']) === false)
			$errors[] = ACP3_CMS::$lang->t('users', 'invalid_birthday');
		if (ACP3_Validate::email($_POST['mail']) === false)
			$errors['mail'] = ACP3_CMS::$lang->t('system', 'wrong_email_format');
		if (userEmailExists($_POST['mail'], ACP3_CMS::$auth->getUserId()) === true)
			$errors['mail'] = ACP3_CMS::$lang->t('users', 'user_email_already_exists');
		if (!empty($_POST['icq']) && ACP3_Validate::icq($_POST['icq']) === false)
			$errors['icq'] = ACP3_CMS::$lang->t('users', 'invalid_icq_number');
		if (!empty($_POST['new_pwd']) && !empty($_POST['new_pwd_repeat']) && $_POST['new_pwd'] != $_POST['new_pwd_repeat'])
			$errors[] = ACP3_CMS::$lang->t('users', 'type_in_pwd');

		if (isset($errors) === true) {
			ACP3_CMS::$view->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
		} else {
			$update_values = array(
				'nickname' => str_encode($_POST['nickname']),
				'realname' => str_encode($_POST['realname']),
				'gender' => (int) $_POST['gender'],
				'birthday' => $_POST['birthday'],
				'birthday_display' => (int) $_POST['birthday_display'],
				'mail' => $_POST['mail'],
				'mail_display' => isset($_POST['mail_display']) ? 1 : 0,
				'website' => str_encode($_POST['website']),
				'icq' => $_POST['icq'],
				'skype' => str_encode($_POST['skype']),
				'street' => str_encode($_POST['street']),
				'house_number' => str_encode($_POST['house_number']),
				'zip' => str_encode($_POST['zip']),
				'city' => str_encode($_POST['city']),
				'address_display' => isset($_POST['address_display']) ? 1 : 0,
				'country' => str_encode($_POST['country']),
				'country_display' => isset($_POST['country_display']) ? 1 : 0,
			);

			// Neues Passwort
			if (!empty($_POST['new_pwd']) && !empty($_POST['new_pwd_repeat'])) {
				$salt = salt(12);
				$new_pwd = generateSaltedPassword($salt, $_POST['new_pwd']);
				$update_values['pwd'] = $new_pwd . ':' . $salt;
			}

			$bool = ACP3_CMS::$db2->update(DB_PRE . 'users', $update_values, array('id' => ACP3_CMS::$auth->getUserId()));

			$cookie_arr = explode('|', base64_decode($_COOKIE['ACP3_AUTH']));
			ACP3_CMS::$auth->setCookie($_POST['nickname'], isset($new_pwd) ? $new_pwd : $cookie_arr[1], 3600);

			ACP3_CMS::$session->unsetFormToken();

			setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'users/home');
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$user = ACP3_CMS::$auth->getUserInfo();

		// Geschlecht
		$gender = array();
		$gender[0]['value'] = '1';
		$gender[0]['selected'] = selectEntry('gender', 1, $user['gender']);
		$gender[0]['lang'] = ACP3_CMS::$lang->t('users', 'gender_not_specified');
		$gender[1]['value'] = '2';
		$gender[1]['selected'] = selectEntry('gender', 2, $user['gender']);
		$gender[1]['lang'] = ACP3_CMS::$lang->t('users', 'gender_female');
		$gender[2]['value'] = '3';
		$gender[2]['selected'] = selectEntry('gender', 3, $user['gender']);
		$gender[2]['lang'] = ACP3_CMS::$lang->t('users', 'gender_male');
		ACP3_CMS::$view->assign('gender', $gender);

		// Geburtstag
		ACP3_CMS::$view->assign('birthday_datepicker', ACP3_CMS::$date->datepicker('birthday', $user['birthday'], 'Y-m-d', array('constrainInput' => 'true', 'changeMonth' => 'true', 'changeYear' => 'true', 'yearRange' => '\'-50:+0\''), 0, 1, false, true));
		$birthday_display = array();
		$birthday_display[0]['name'] = 'hide';
		$birthday_display[0]['value'] = '0';
		$birthday_display[0]['checked'] = selectEntry('birthday_display', '0', $user['birthday_display'], 'checked');
		$birthday_display[0]['lang'] = ACP3_CMS::$lang->t('users', 'birthday_hide');
		$birthday_display[1]['name'] = 'full';
		$birthday_display[1]['value'] = '1';
		$birthday_display[1]['checked'] = selectEntry('birthday_display', '1', $user['birthday_display'], 'checked');
		$birthday_display[1]['lang'] = ACP3_CMS::$lang->t('users', 'birthday_display_completely');
		$birthday_display[2]['name'] = 'hide_year';
		$birthday_display[2]['value'] = '2';
		$birthday_display[2]['checked'] = selectEntry('birthday_display', '2', $user['birthday_display'], 'checked');
		$birthday_display[2]['lang'] = ACP3_CMS::$lang->t('users', 'birthday_hide_year');
		ACP3_CMS::$view->assign('birthday_display', $birthday_display);

		// Kontaktangaben
		$contact = array();
		$contact[0]['name'] = 'mail';
		$contact[0]['lang'] = ACP3_CMS::$lang->t('system', 'email_address');
		$contact[0]['value'] = isset($_POST['submit']) ? $_POST['mail'] : $user['mail'];
		$contact[0]['maxlength'] = '120';
		$contact[1]['name'] = 'website';
		$contact[1]['lang'] = ACP3_CMS::$lang->t('system', 'website');
		$contact[1]['value'] = isset($_POST['submit']) ? $_POST['website'] : $user['website'];
		$contact[1]['maxlength'] = '120';
		$contact[2]['name'] = 'icq';
		$contact[2]['lang'] = ACP3_CMS::$lang->t('users', 'icq');
		$contact[2]['value'] = isset($_POST['submit']) ? $_POST['icq'] : $user['icq'];
		$contact[2]['maxlength'] = '9';
		$contact[3]['name'] = 'skype';
		$contact[3]['lang'] = ACP3_CMS::$lang->t('users', 'skype');
		$contact[3]['value'] = isset($_POST['submit']) ? $_POST['skype'] : $user['skype'];
		$contact[3]['maxlength'] = '28';
		ACP3_CMS::$view->assign('contact', $contact);

		$address = array();
		$address[0]['name'] = 'address_display';
		$address[0]['checked'] = selectEntry('address_display', '1', $user['address_display'], 'checked');
		$address[0]['lang'] = ACP3_CMS::$lang->t('users', 'display_address');
		$address[1]['name'] = 'country_display';
		$address[1]['checked'] = selectEntry('country_display', '1', $user['country_display'], 'checked');
		$address[1]['lang'] = ACP3_CMS::$lang->t('users', 'display_country');
		ACP3_CMS::$view->assign('address_checkboxes', $address);

		$countries = ACP3_LANG::worldCountries();
		$countries_select = array();
		foreach ($countries as $key => $value) {
			$countries_select[] = array(
				'value' => $key,
				'lang' => $value,
				'selected' => selectEntry('countries', $key, $user['country']),
			);
		}
		ACP3_CMS::$view->assign('countries', $countries_select);

		ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : $user);

		ACP3_CMS::$session->generateFormToken();

		ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('users/edit_profile.tpl'));
	}
}
