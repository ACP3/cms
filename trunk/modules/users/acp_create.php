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
	if (empty($_POST['pwd']) || empty($_POST['pwd_repeat']) || $_POST['pwd'] != $_POST['pwd_repeat'])
		$errors[] = ACP3_CMS::$lang->t('users', 'type_in_pwd');

	if (isset($errors) === true) {
		ACP3_CMS::$view->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
	} else {
		$salt = salt(12);

		$insert_values = array(
			'id' => '',
			'super_user' => (int) $_POST['super_user'],
			'nickname' => str_encode($_POST['nickname']),
			'pwd' => generateSaltedPassword($salt, $_POST['pwd']) . ':' . $salt,
			'realname' => str_encode($_POST['realname']) . ':1',
			'gender' => ':1',
			'birthday' => ':1',
			'birthday_format' => '1',
			'mail' => $_POST['mail'] . ':1',
			'website' => str_encode($_POST['website']) . ':1',
			'icq' => ':1',
			'msn' => ':1',
			'skype' => ':1',
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
	$super_user = array();
	$super_user[0]['value'] = '1';
	$super_user[0]['checked'] = selectEntry('super_user', '1', '0', 'checked');
	$super_user[0]['lang'] = ACP3_CMS::$lang->t('system', 'yes');
	$super_user[1]['value'] = '0';
	$super_user[1]['checked'] = selectEntry('super_user', '0', '0', 'checked');
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

	$defaults = array(
		'nickname' => '',
		'realname' => '',
		'mail' => '',
		'website' => '',
		'date_format_long' => CONFIG_DATE_FORMAT_LONG,
		'date_format_short' => CONFIG_DATE_FORMAT_SHORT
	);

	ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : $defaults);

	ACP3_CMS::$session->generateFormToken();

	ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('users/acp_create.tpl'));
}