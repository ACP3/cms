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
	$form = $_POST['form'];

	if (empty($form['nickname']))
		$errors[] = lang('common', 'name_to_short');
	if (!empty($form['nickname']) && $db->select('id', 'users', 'nickname = \'' . $db->escape($form['nickname']) . '\'', 0, 0, 0, 1) == '1')
		$errors[] = lang('users', 'user_name_already_exists');
	if (!$validate->email($form['mail']))
		$errors[] = lang('common', 'wrong_email_format');
	if ($validate->email($form['mail']) && $db->select('id', 'users', 'mail =\'' . $form['mail'] . '\'', 0, 0, 0, 1) > 0)
		$errors[] = lang('common', 'user_email_already_exists');
	if (!$validate->is_number($form['access']))
		$errors[] = lang('users', 'select_access_level');
	if (empty($form['pwd']) || empty($form['pwd_repeat']) || $form['pwd'] != $form['pwd_repeat'])
		$errors[] = lang('users', 'type_in_pwd');

	if (isset($errors)) {
		$tpl->assign('error_msg', combo_box($errors));
	} else {
		$salt = salt(12);

		$insert_values = array(
			'id' => '',
			'nickname' => $db->escape($form['nickname']),
			'realname' => '',
			'pwd' => sha1($salt . sha1($form['pwd'])) . ':' . $salt,
			'access' => $form['access'],
			'mail' => $form['mail'],
			'website' => '',
			'time_zone' => CONFIG_TIME_ZONE,
			'dst' => CONFIG_DST,
			'language' => CONFIG_LANG,
			'draft' => '',
		);

		$bool = $db->insert('users', $insert_values);

		$content = combo_box($bool ? lang('users', 'create_success') : lang('users', 'create_error'), uri('acp/users'));
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	$access = $db->select('id, name', 'access', 0, 'name ASC');
	$c_access = count($access);

	for ($i = 0; $i < $c_access; $i++) {
		$access[$i]['name'] = $access[$i]['name'];
		$access[$i]['selected'] = select_entry('access', $access[$i]['id']);
	}
	$tpl->assign('access', $access);

	$tpl->assign('form', isset($form) ? $form : '');

	$content = $tpl->fetch('users/create.html');
}
?>