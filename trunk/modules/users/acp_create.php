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

if (isset($_POST['submit']) === true) {
	require_once MODULES_DIR . 'users/functions.php';

	if (empty($_POST['nickname']))
		$errors['nickname'] = ACP3_CMS::$lang->t('system', 'name_to_short');
	if (ACP3_Validate::gender($_POST['gender']) === false)
		$errors['gender'] = ACP3_CMS::$lang->t('users', 'select_gender');
	if (!empty($_POST['birthday']) && ACP3_Validate::birthday($_POST['birthday']) === false)
		$errors[] = ACP3_CMS::$lang->t('users', 'invalid_birthday');
	if (userNameExists($_POST['nickname']) === true)
		$errors['nickname'] = ACP3_CMS::$lang->t('users', 'user_name_already_exists');
	if (ACP3_Validate::email($_POST['mail']) === false)
		$errors['mail'] = ACP3_CMS::$lang->t('system', 'wrong_email_format');
	if (userEmailExists($_POST['mail']) === true)
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
	if (empty($_POST['pwd']) || empty($_POST['pwd_repeat']) || $_POST['pwd'] != $_POST['pwd_repeat'])
		$errors[] = ACP3_CMS::$lang->t('users', 'type_in_pwd');

	if (isset($errors) === true) {
		ACP3_CMS::$view->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_CMS::$view->setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
	} else {
		$salt = salt(12);

		$insert_values = array(
			'id' => '',
			'super_user' => (int) $_POST['super_user'],
			'nickname' => str_encode($_POST['nickname']),
			'pwd' => generateSaltedPassword($salt, $_POST['pwd']) . ':' . $salt,
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
			'date_format_long' => str_encode($_POST['date_format_long']),
			'date_format_short' => str_encode($_POST['date_format_short']),
			'time_zone' => $_POST['date_time_zone'],
			'language' => $_POST['language'],
			'entries' => (int) $_POST['entries'],
			'draft' => '',
		);

		ACP3_CMS::$db2->beginTransaction();
		try {
			$bool = ACP3_CMS::$db2->insert(DB_PRE . 'users', $insert_values);
			$user_id = ACP3_CMS::$db2->lastInsertId();
			foreach ($_POST['roles'] as $row) {
				ACP3_CMS::$db2->insert(DB_PRE . 'acl_user_roles', array('user_id' => $user_id, 'role_id' => $row));
			}
			ACP3_CMS::$db2->commit();
		} catch (Exception $e) {
			ACP3_CMS::$db2->rollback();
			$bool = false;
		}

		ACP3_CMS::$session->unsetFormToken();

		setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/users');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	// Zugriffslevel holen
	$roles = ACP3_ACL::getAllRoles();
	$c_roles = count($roles);
	for ($i = 0; $i < $c_roles; ++$i) {
		$roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
		$roles[$i]['selected'] = selectEntry('roles', $roles[$i]['id']);
	}
	ACP3_CMS::$view->assign('roles', $roles);

	// Super User
	$lang_super_user = array(ACP3_CMS::$lang->t('system', 'yes'), ACP3_CMS::$lang->t('system', 'no'));
	ACP3_CMS::$view->assign('super_user', selectGenerator('super_user', array(1, 0), $lang_super_user, 0, 'checked'));

	// Sprache
	$languages = array();
	$lang_dir = scandir(ACP3_ROOT . 'languages');
	$c_lang_dir = count($lang_dir);
	for ($i = 0; $i < $c_lang_dir; ++$i) {
		$lang_info = ACP3_XML::parseXmlFile(ACP3_ROOT . 'languages/' . $lang_dir[$i] . '/info.xml', '/language');
		if (!empty($lang_info)) {
			$name = $lang_info['name'];
			$languages[$name]['dir'] = $lang_dir[$i];
			$languages[$name]['selected'] = selectEntry('language', $lang_dir[$i], CONFIG_LANG);
			$languages[$name]['name'] = $lang_info['name'];
		}
	}
	ksort($languages);
	ACP3_CMS::$view->assign('languages', $languages);

	// Einträge pro Seite
	ACP3_CMS::$view->assign('entries', recordsPerPage(CONFIG_ENTRIES));

	// Zeitzonen
	ACP3_CMS::$view->assign('time_zones', ACP3_CMS::$date->getTimeZones(CONFIG_DATE_TIME_ZONE));

	// Geschlecht
	$lang_gender = array(
		ACP3_CMS::$lang->t('users', 'gender_not_specified'),
		ACP3_CMS::$lang->t('users', 'gender_female'),
		ACP3_CMS::$lang->t('users', 'gender_male')
	);
	ACP3_CMS::$view->assign('gender', selectGenerator('gender', array(1, 2, 3), $lang_gender, ''));

	// Geburtstag
	ACP3_CMS::$view->assign('birthday_datepicker', ACP3_CMS::$date->datepicker('birthday', '', 'Y-m-d', array('constrainInput' => 'true', 'changeMonth' => 'true', 'changeYear' => 'true', 'yearRange' => '\'-50:+0\''), 0, 1, false, true));

	// Kontaktangaben
	$contact = array();
	$contact[0]['name'] = 'mail';
	$contact[0]['lang'] = ACP3_CMS::$lang->t('system', 'email_address');
	$contact[0]['value'] = isset($_POST['submit']) ? $_POST['mail'] : '';
	$contact[0]['maxlength'] = '120';
	$contact[1]['name'] = 'website';
	$contact[1]['lang'] = ACP3_CMS::$lang->t('system', 'website');
	$contact[1]['value'] = isset($_POST['submit']) ? $_POST['website'] : '';
	$contact[1]['maxlength'] = '120';
	$contact[2]['name'] = 'icq';
	$contact[2]['lang'] = ACP3_CMS::$lang->t('users', 'icq');
	$contact[2]['value'] = isset($_POST['submit']) ? $_POST['icq'] : '';
	$contact[2]['maxlength'] = '9';
	$contact[3]['name'] = 'skype';
	$contact[3]['lang'] = ACP3_CMS::$lang->t('users', 'skype');
	$contact[3]['value'] = isset($_POST['submit']) ? $_POST['skype'] : '';
	$contact[3]['maxlength'] = '28';
	ACP3_CMS::$view->assign('contact', $contact);

	$countries = ACP3_LANG::worldCountries();
	$countries_select = array();
	foreach ($countries as $key => $value) {
		$countries_select[] = array(
			'value' => $key,
			'lang' => $value,
			'selected' => selectEntry('countries', $key),
		);
	}
	ACP3_CMS::$view->assign('countries', $countries_select);

	$lang_mail_display = array(ACP3_CMS::$lang->t('system', 'yes'), ACP3_CMS::$lang->t('system', 'no'));
	ACP3_CMS::$view->assign('mail_display', selectGenerator('mail_display', array(1, 0), $lang_mail_display, 0, 'checked'));

	$lang_address_display = array(ACP3_CMS::$lang->t('system', 'yes'), ACP3_CMS::$lang->t('system', 'no'));
	ACP3_CMS::$view->assign('address_display', selectGenerator('address_display', array(1, 0), $lang_address_display, 0, 'checked'));

	$lang_country_display = array(ACP3_CMS::$lang->t('system', 'yes'), ACP3_CMS::$lang->t('system', 'no'));
	ACP3_CMS::$view->assign('country_display', selectGenerator('country_display', array(1, 0), $lang_country_display, 0, 'checked'));

	$lang_birthday_display = array(
		ACP3_CMS::$lang->t('users', 'birthday_hide'),
		ACP3_CMS::$lang->t('users', 'birthday_display_completely'),
		ACP3_CMS::$lang->t('users', 'birthday_hide_year')
	);
	ACP3_CMS::$view->assign('birthday_display', selectGenerator('birthday_display', array(0, 1, 2), $lang_birthday_display, 0, 'checked'));

	$defaults = array(
		'nickname' => '',
		'realname' => '',
		'mail' => '',
		'website' => '',
		'street' => '',
		'house_number' => '',
		'zip' => '',
		'city' => '',
		'date_format_long' => CONFIG_DATE_FORMAT_LONG,
		'date_format_short' => CONFIG_DATE_FORMAT_SHORT
	);

	ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : $defaults);

	ACP3_CMS::$session->generateFormToken();
}