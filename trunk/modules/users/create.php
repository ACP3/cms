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
		$errors[] = $lang->t('common', 'name_to_short');
	if (userNameExists($form['nickname']))
		$errors[] = $lang->t('users', 'user_name_already_exists');
	if (!validate::email($form['mail']))
		$errors[] = $lang->t('common', 'wrong_email_format');
	if (userEmailExists($form['mail']))
		$errors[] = $lang->t('users', 'user_email_already_exists');
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

		$content = comboBox($bool ? $lang->t('common', 'create_success') : $lang->t('common', 'create_error'), uri('acp/users'));
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	$access = $db->select('id, name', 'access', 0, 'name ASC');
	$c_access = count($access);

	for ($i = 0; $i < $c_access; ++$i) {
		$access[$i]['name'] = $access[$i]['name'];
		$access[$i]['selected'] = selectEntry('access', $access[$i]['id']);
	}
	$tpl->assign('access', $access);

	$defaults = array(
		'nickname' => '',
		'mail' => '',
	);

	$tpl->assign('form', isset($form) ? $form : $defaults);

	$content = $tpl->fetch('users/create.html');
}
?>