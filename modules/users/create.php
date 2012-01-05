<?php
/**
 * Users
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['form'])) {
	require_once MODULES_DIR . 'users/functions.php';

	$form = $_POST['form'];

	if (empty($form['nickname']))
		$errors[] = $lang->t('common', 'name_to_short');
	if (!validate::email($form['mail']))
		$errors[] = $lang->t('common', 'wrong_email_format');
	if (userNameExists($form['nickname']))
		$errors[] = $lang->t('users', 'user_name_already_exists');
	if (userEmailExists($form['mail']))
		$errors[] = $lang->t('users', 'user_email_already_exists');
	if (!validate::isNumber($form['entries']))
		$errors[] = $lang->t('system', 'select_entries_per_page');
	if (empty($form['date_format_long']) || empty($form['date_format_short']))
		$errors[] = $lang->t('system', 'type_in_date_format');
	if (!is_numeric($form['time_zone']))
		$errors[] = $lang->t('common', 'select_time_zone');
	if (!validate::isNumber($form['dst']))
		$errors[] = $lang->t('common', 'select_daylight_saving_time');
	if (preg_match('=/=', $form['language']) || !is_file('languages/' . $form['language'] . '/info.xml'))
		$errors[] = $lang->t('users', 'select_language');
	if (empty($form['roles']) || !is_array($form['roles']) || !validate::aclRolesExist($form['roles']))
		$errors[] = $lang->t('users', 'select_access_level');
	if (empty($form['pwd']) || empty($form['pwd_repeat']) || $form['pwd'] != $form['pwd_repeat'])
		$errors[] = $lang->t('users', 'type_in_pwd');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$salt = salt(12);

		$insert_values = array(
			'id' => '',
			'nickname' => $db->escape($form['nickname']),
			'pwd' => genSaltedPassword($salt, $form['pwd']) . ':' . $salt,
			'realname' => $db->escape($form['realname']) . ':1',
			'gender' => ':1',
			'birthday' => ':1',
			'birthday_format' => '1',
			'mail' => $form['mail'] . ':1',
			'website' => $db->escape($form['website'], 2) . ':1',
			'icq' => ':1',
			'msn' => ':1',
			'skype' => ':1',
			'date_format_long' => $db->escape($form['date_format_long']),
			'date_format_short' => $db->escape($form['date_format_short']),
			'time_zone' => $form['time_zone'],
			'dst' => $form['dst'],
			'language' => $db->escape($form['language'], 2),
			'entries' => (int) $form['entries'],
			'draft' => '',
		);

		$db->link->beginTransaction();
		$bool = $db->insert('users', $insert_values);

		$user_id = $db->link->lastInsertId();
		foreach ($form['roles'] as $row) {
			$db->insert('acl_user_roles', array('user_id' => $user_id, 'role_id' => $row));
		}

		$db->link->commit();

		$content = comboBox($bool ? $lang->t('common', 'create_success') : $lang->t('common', 'create_error'), $uri->route('acp/users'));
	}
}
if (!isset($_POST['form']) || isset($errors) && is_array($errors)) {
	// Zugriffslevel holen
	$roles = acl::getAllRoles();
	$c_roles = count($roles);
	for ($i = 0; $i < $c_roles; ++$i) {
		$roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
		$roles[$i]['selected'] = selectEntry('roles', $roles[$i]['id']);
	}
	$tpl->assign('roles', $roles);

	// Sprache
	$languages = array();
	$lang_dir = scandir(ACP3_ROOT . 'languages');
	$c_lang_dir = count($lang_dir);
	for ($i = 0; $i < $c_lang_dir; ++$i) {
		$lang_info = xml::parseXmlFile(ACP3_ROOT . 'languages/' . $lang_dir[$i] . '/info.xml', '/language');
		if (!empty($lang_info)) {
			$name = $lang_info['name'];
			$languages[$name]['dir'] = $lang_dir[$i];
			$languages[$name]['selected'] = selectEntry('language', $lang_dir[$i], CONFIG_LANG);
			$languages[$name]['name'] = $lang_info['name'];
		}
	}
	ksort($languages);
	$tpl->assign('languages', $languages);

	// Einträge pro Seite
	for ($i = 0, $j = 10; $j <= 50; $i++, $j = $j + 10) {
		$entries[$i]['value'] = $j;
		$entries[$i]['selected'] = selectEntry('entries', $j, CONFIG_ENTRIES);
	}
	$tpl->assign('entries', $entries);

	// Zeitzonen
	$tpl->assign('time_zone', timeZones(CONFIG_DATE_TIME_ZONE));

	// Sommerzeit an/aus
	$dst[0]['value'] = '1';
	$dst[0]['checked'] = selectEntry('dst', '1', CONFIG_DATE_DST, 'checked');
	$dst[0]['lang'] = $lang->t('common', 'yes');
	$dst[1]['value'] = '0';
	$dst[1]['checked'] = selectEntry('dst', '0', CONFIG_DATE_DST, 'checked');
	$dst[1]['lang'] = $lang->t('common', 'no');
	$tpl->assign('dst', $dst);

	$defaults = array(
		'nickname' => '',
		'realname' => '',
		'mail' => '',
		'website' => '',
		'date_format_long' => CONFIG_DATE_FORMAT_LONG,
		'date_format_short' => CONFIG_DATE_FORMAT_SHORT
	);

	$tpl->assign('form', isset($form) ? $form : $defaults);

	$content = modules::fetchTemplate('users/create.html');
}
