<?php
/**
 * Users
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (isset($_POST['submit'])) {
	require_once ACP3_ROOT . 'modules/users/functions.php';

	$form = $_POST['form'];

	if (empty($form['nickname']))
		$errors[] = $lang->t('common', 'name_to_short');
	if (userNameExists($form['nickname']))
		$errors[] = $lang->t('users', 'user_name_already_exists');
	if (!validate::email($form['mail']))
		$errors[] = $lang->t('common', 'wrong_email_format');
	if (userEmailExists($form['mail']))
		$errors[] = $lang->t('users', 'user_email_already_exists');
	if (!is_numeric($form['time_zone']))
		$errors[] = $lang->t('common', 'select_time_zone');
	if (!validate::isNumber($form['dst']))
		$errors[] = $lang->t('common', 'select_daylight_saving_time');
	if (preg_match('=/=', $form['language']) || !is_file('languages/' . $form['language'] . '/info.xml'))
		$errors[] = $lang->t('users', 'select_language');
	if (!validate::isNumber($form['access']))
		$errors[] = $lang->t('users', 'select_access_level');
	if (empty($form['pwd']) || empty($form['pwd_repeat']) || $form['pwd'] != $form['pwd_repeat'])
		$errors[] = $lang->t('users', 'type_in_pwd');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$salt = salt(12);

		$insert_values = array(
			'id' => '',
			'nickname' => db::escape($form['nickname']),
			'pwd' => genSaltedPassword($salt, $form['pwd']) . ':' . $salt,
			'access' => $form['access'],
			'realname' => db::escape($form['realname']) . ':1',
			'gender' => ':1',
			'birthday' => ':1',
			'birthday_format' => '1',
			'mail' => $form['mail'] . ':1',
			'website' => db::escape($form['website'], 2) . ':1',
			'icq' => ':1',
			'msn' => ':1',
			'skype' => ':1',
			'time_zone' => $form['time_zone'],
			'dst' => $form['dst'],
			'language' => db::escape($form['language'], 2),
			'draft' => '',
		);

		$bool = $db->insert('users', $insert_values);

		$content = comboBox($bool ? $lang->t('common', 'create_success') : $lang->t('common', 'create_error'), uri('acp/users'));
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
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

	$access = $db->select('id, name', 'access', 0, 'name ASC');
	$c_access = count($access);

	for ($i = 0; $i < $c_access; ++$i) {
		$access[$i]['name'] = $access[$i]['name'];
		$access[$i]['selected'] = selectEntry('access', $access[$i]['id']);
	}
	$tpl->assign('access', $access);

	$defaults = array(
		'nickname' => '',
		'realname' => '',
		'mail' => '',
		'website' => '',
	);

	$tpl->assign('form', isset($form) ? $form : $defaults);

	$content = $tpl->fetch('users/create.html');
}
