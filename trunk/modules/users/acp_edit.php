<?php
/**
 * Users
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true &&
	ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'users WHERE id = ?', array(ACP3_CMS::$uri->id)) == 1) {
	$user = ACP3_CMS::$auth->getUserInfo(ACP3_CMS::$uri->id);

	if (isset($_POST['submit']) === true) {
		require_once MODULES_DIR . 'users/functions.php';

		if (empty($_POST['nickname']))
			$errors['nickname'] = ACP3_CMS::$lang->t('system', 'name_to_short');
		if (ACP3_Validate::gender($_POST['gender']) === false)
			$errors['gender'] = ACP3_CMS::$lang->t('users', 'select_gender');
		if (!empty($_POST['birthday']) && ACP3_Validate::birthday($_POST['birthday']) === false)
			$errors[] = ACP3_CMS::$lang->t('users', 'invalid_birthday');
		if (userNameExists($_POST['nickname'], ACP3_CMS::$uri->id))
			$errors['nickname'] = ACP3_CMS::$lang->t('users', 'user_name_already_exists');
		if (ACP3_Validate::email($_POST['mail']) === false)
			$errors['mail'] = ACP3_CMS::$lang->t('system', 'wrong_email_format');
		if (userEmailExists($_POST['mail'], ACP3_CMS::$uri->id))
			$errors['mail'] = ACP3_CMS::$lang->t('users', 'user_email_already_exists');
		if (empty($_POST['roles']) || is_array($_POST['roles']) === false || ACP3_Validate::aclRolesExist($_POST['roles']) === false)
			$errors['roles'] = ACP3_CMS::$lang->t('users', 'select_access_level');
		if (!isset($_POST['super_user']) || ($_POST['super_user'] != 1 && $_POST['super_user'] != 0))
			$errors['super-user'] = ACP3_CMS::$lang->t('users', 'select_super_user');
		if (ACP3_CMS::$lang->languagePackExists($_POST['language']) === false)
			$errors['language'] = ACP3_CMS::$lang->t('users', 'select_language');
		if (ACP3_Validate::isNumber($_POST['entries']) === false)
			$errors['entries'] = ACP3_CMS::$lang->t('system', 'select_records_per_page');
		if (empty($_POST['date_format_long']) || empty($_POST['date_format_short']))
			$errors[] = ACP3_CMS::$lang->t('system', 'type_in_date_format');
		if (ACP3_Validate::timeZone($_POST['date_time_zone']) === false)
			$errors['time-zone'] = ACP3_CMS::$lang->t('system', 'select_time_zone');
		if (!empty($_POST['icq']) && ACP3_Validate::icq($_POST['icq']) === false)
			$errors['icq'] = ACP3_CMS::$lang->t('users', 'invalid_icq_number');
		if (in_array($_POST['mail_display'], array(0, 1)) === false)
			$errors[] = ACP3_CMS::$lang->t('users', 'select_mail_display');
		if (in_array($_POST['address_display'], array(0, 1)) === false)
			$errors[] = ACP3_CMS::$lang->t('users', 'select_address_display');
		if (in_array($_POST['country_display'], array(0, 1)) === false)
			$errors[] = ACP3_CMS::$lang->t('users', 'select_country_display');
		if (in_array($_POST['birthday_display'], array(0, 1, 2)) === false)
			$errors[] = ACP3_CMS::$lang->t('users', 'select_birthday_display');
		if (!empty($_POST['new_pwd']) && !empty($_POST['new_pwd_repeat']) && $_POST['new_pwd'] != $_POST['new_pwd_repeat'])
			$errors[] = ACP3_CMS::$lang->t('users', 'type_in_pwd');

		if (isset($errors) === true) {
			ACP3_CMS::$view->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_CMS::$view->setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
		} else {
			$update_values = array(
				'super_user' => (int) $_POST['super_user'],
				'nickname' => str_encode($_POST['nickname']),
				'realname' => str_encode($_POST['realname']),
				'gender' => (int) $_POST['gender'],
				'birthday' => $_POST['birthday'],
				'birthday_display' => (int) $_POST['birthday_display'],
				'mail' => $_POST['mail'],
				'mail_display' => (int) $_POST['mail_display'],
				'website' => str_encode($_POST['website']),
				'icq' => $_POST['icq'],
				'skype' => str_encode($_POST['skype']),
				'street' => str_encode($_POST['street']),
				'house_number' => str_encode($_POST['house_number']),
				'zip' => str_encode($_POST['zip']),
				'city' => str_encode($_POST['city']),
				'address_display' => (int) $_POST['address_display'],
				'country' => str_encode($_POST['country']),
				'country_display' => (int) $_POST['country_display'],
				'date_format_long' => str_encode($_POST['date_format_long']),
				'date_format_short' => str_encode($_POST['date_format_short']),
				'time_zone' => $_POST['date_time_zone'],
				'language' => $_POST['language'],
				'entries' => (int) $_POST['entries'],
			);

			// Rollen aktualisieren
			ACP3_CMS::$db2->beginTransaction();
			try {
				ACP3_CMS::$db2->delete(DB_PRE . 'acl_user_roles', array('user_id' => ACP3_CMS::$uri->id));
				foreach ($_POST['roles'] as $row) {
					ACP3_CMS::$db2->insert(DB_PRE . 'acl_user_roles', array('user_id' => ACP3_CMS::$uri->id, 'role_id' => $row));
				}
				ACP3_CMS::$db2->commit();
			} catch (Exception $e) {
				ACP3_CMS::$db2->rollback();
			}

			// Neues Passwort
			if (!empty($_POST['new_pwd']) && !empty($_POST['new_pwd_repeat'])) {
				$salt = salt(12);
				$new_pwd = generateSaltedPassword($salt, $_POST['new_pwd']);
				$update_values['pwd'] = $new_pwd . ':' . $salt;
			}

			$bool = ACP3_CMS::$db2->update(DB_PRE . 'users', $update_values, array('id' => ACP3_CMS::$uri->id));

			// Falls sich der User selbst bearbeitet hat, Cookie aktualisieren
			if (ACP3_CMS::$uri->id == ACP3_CMS::$auth->getUserId()) {
				$cookie_arr = explode('|', base64_decode($_COOKIE['ACP3_AUTH']));
				ACP3_CMS::$auth->setCookie($_POST['nickname'], isset($new_pwd) ? $new_pwd : $cookie_arr[1], 3600);
			}

			ACP3_CMS::$session->unsetFormToken();

			setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/users');
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		// Zugriffslevel holen
		$roles = ACP3_ACL::getAllRoles();
		$c_roles = count($roles);
		$user_roles = ACP3_ACL::getUserRoles(ACP3_CMS::$uri->id);
		for ($i = 0; $i < $c_roles; ++$i) {
			$roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
			$roles[$i]['selected'] = selectEntry('roles', $roles[$i]['id'], in_array($roles[$i]['id'], $user_roles) ? $roles[$i]['id'] : '');
		}
		ACP3_CMS::$view->assign('roles', $roles);

		// Super User
		$super_user = array();
		$super_user[0]['value'] = '1';
		$super_user[0]['checked'] = selectEntry('super_user', '1', $user['super_user'], 'checked');
		$super_user[0]['lang'] = ACP3_CMS::$lang->t('system', 'yes');
		$super_user[1]['value'] = '0';
		$super_user[1]['checked'] = selectEntry('super_user', '0', $user['super_user'], 'checked');
		$super_user[1]['lang'] = ACP3_CMS::$lang->t('system', 'no');
		ACP3_CMS::$view->assign('super_user', $super_user);

		// Sprache
		$languages = array();
		$lang_dir = scandir(ACP3_ROOT . 'languages');
		$c_lang_dir = count($lang_dir);
		for ($i = 0; $i < $c_lang_dir; ++$i) {
			$lang_info = ACP3_XML::parseXmlFile(ACP3_ROOT . 'languages/' . $lang_dir[$i] . '/info.xml', '/language');
			if (!empty($lang_info)) {
				$name = $lang_info['name'];
				$languages[$name]['dir'] = $lang_dir[$i];
				$languages[$name]['selected'] = selectEntry('language', $lang_dir[$i], $user['language']);
				$languages[$name]['name'] = $lang_info['name'];
			}
		}
		ksort($languages);
		ACP3_CMS::$view->assign('languages', $languages);

		// Einträge pro Seite
		ACP3_CMS::$view->assign('entries', recordsPerPage((int) $user['entries']));

		// Zeitzonen
		ACP3_CMS::$view->assign('time_zones', ACP3_CMS::$date->getTimeZones($user['time_zone']));

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

		$mail_display = array();
		$mail_display[0]['value'] = '1';
		$mail_display[0]['checked'] = selectEntry('mail_display', '1', $user['mail_display'], 'checked');
		$mail_display[0]['lang'] = ACP3_CMS::$lang->t('system', 'yes');
		$mail_display[1]['value'] = '0';
		$mail_display[1]['checked'] = selectEntry('mail_display', '0', $user['mail_display'], 'checked');
		$mail_display[1]['lang'] = ACP3_CMS::$lang->t('system', 'no');
		ACP3_CMS::$view->assign('mail_display', $mail_display);

		$address_display = array();
		$address_display[0]['value'] = '1';
		$address_display[0]['checked'] = selectEntry('address_display', '1', $user['address_display'], 'checked');
		$address_display[0]['lang'] = ACP3_CMS::$lang->t('system', 'yes');
		$address_display[1]['value'] = '0';
		$address_display[1]['checked'] = selectEntry('address_display', '0', $user['address_display'], 'checked');
		$address_display[1]['lang'] = ACP3_CMS::$lang->t('system', 'no');
		ACP3_CMS::$view->assign('address_display', $address_display);

		$country_display = array();
		$country_display[0]['value'] = '1';
		$country_display[0]['checked'] = selectEntry('country_display', '1', $user['country_display'], 'checked');
		$country_display[0]['lang'] = ACP3_CMS::$lang->t('system', 'yes');
		$country_display[1]['value'] = '0';
		$country_display[1]['checked'] = selectEntry('country_display', '0', $user['country_display'], 'checked');
		$country_display[1]['lang'] = ACP3_CMS::$lang->t('system', 'no');
		ACP3_CMS::$view->assign('country_display', $country_display);

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

		ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : $user);

		ACP3_CMS::$session->generateFormToken();
	}
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}