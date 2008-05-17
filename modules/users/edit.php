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

if (validate::isNumber($uri->id) && $db->select('id', 'users', 'id = \'' . $uri->id . '\'', 0, 0, 0, 1) == '1') {
	if (isset($_POST['submit'])) {
		$form = $_POST['form'];

		if (empty($form['nickname']))
			$errors[] = lang('common', 'name_to_short');
		if (!empty($form['nickname']) && $db->select('id', 'users', 'id != \'' . $uri->id . '\' AND nickname = \'' . $db->escape($form['nickname']) . '\'', 0, 0, 0, 1) == '1')
			$errors[] = lang('users', 'user_name_already_exists');
		if (!validate::email($form['mail']))
			$errors[] = lang('common', 'wrong_email_format');
		if (validate::email($form['mail']) && $db->select('id', 'users', 'id != \'' . $uri->id . '\' AND mail =\'' . $form['mail'] . '\'', 0, 0, 0, 1) > 0)
			$errors[] = lang('users', 'user_email_already_exists');
		if (!validate::isNumber($form['access']))
			$errors[] = lang('users', 'select_access_level');
		if (!empty($form['new_pwd']) && !empty($form['new_pwd_repeat']) && $form['new_pwd'] != $form['new_pwd_repeat'])
			$errors[] = lang('users', 'type_in_pwd');

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
				'access' => $form['access'],
				'mail' => $form['mail'],
			);
			if (is_array($new_pwd_sql)) {
				$update_values = array_merge($update_values, $new_pwd_sql);
			}

			$bool = $db->update('users', $update_values, 'id = \'' . $uri->id . '\'');

			// Falls sich der User selbst bearbeitet hat, Cookies und Session aktualisieren
			if ($uri->id == USER_ID) {
				$cookie_arr = explode('|', base64_decode($_COOKIE['ACP3_AUTH']));
				$cookie_value = base64_encode($form['nickname'] . '|' . (isset($new_pwd) ? $new_pwd : $cookie_arr[1]));
				setcookie('ACP3_AUTH', $cookie_value, time() + 3600, '/');
			}

			$content = comboBox($bool ? lang('users', 'edit_success') : lang('users', 'edit_error'), uri('acp/users'));
		}
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$user = $db->select('nickname, mail, access', 'users', 'id = \'' . $uri->id . '\'');

		$access = $db->select('id, name', 'access', 0, 'name ASC');
		$c_access = count($access);

		for ($i = 0; $i < $c_access; ++$i) {
			$access[$i]['name'] = $access[$i]['name'];
			$access[$i]['selected'] = selectEntry('access', $access[$i]['id'], $user[0]['access']);
		}
		$tpl->assign('access', $access);

		$tpl->assign('form', isset($form) ? $form : $user[0]);

		$content = $tpl->fetch('users/edit.html');
	}
} else {
	redirect('errors/404');
}
?>