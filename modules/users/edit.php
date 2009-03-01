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

if (validate::isNumber($uri->id) && $db->countRows('*', 'users', 'id = \'' . $uri->id . '\'') == '1') {
	if (isset($_POST['submit'])) {
		$form = $_POST['form'];

		if (empty($form['nickname']))
			$errors[] = $lang->t('common', 'name_to_short');
		if (userNameExists($form['nickname'], $uri->id))
			$errors[] = $lang->t('users', 'user_name_already_exists');
		if (!validate::email($form['mail']))
			$errors[] = $lang->t('common', 'wrong_email_format');
		if (userEmailExists($form['mail'], $uri->id))
			$errors[] = $lang->t('users', 'user_email_already_exists');
		if (!is_numeric($form['time_zone']))
			$errors[] = $lang->t('common', 'select_time_zone');
		if (!validate::isNumber($form['dst']))
			$errors[] = $lang->t('common', 'select_daylight_saving_time');
		if (preg_match('=/=', $form['language']) || !is_file('languages/' . $form['language'] . '/info.xml'))
			$errors[] = $lang->t('users', 'select_language');
		if (!validate::isNumber($form['access']))
			$errors[] = $lang->t('users', 'select_access_level');
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
				'realname' => $db->escape($form['realname']),
				'access' => $form['access'],
				'mail' => $form['mail'],
				'website' => $db->escape($form['website'], 2),
				'time_zone' => $form['time_zone'],
				'dst' => $form['dst'],
				'language' => $db->escape($form['language'], 2),
			);
			if (is_array($new_pwd_sql)) {
				$update_values = array_merge($update_values, $new_pwd_sql);
			}

			$bool = $db->update('users', $update_values, 'id = \'' . $uri->id . '\'');

			// Falls sich der User selbst bearbeitet hat, Cookie aktualisieren
			if ($uri->id == USER_ID) {
				$cookie_arr = explode('|', base64_decode($_COOKIE['ACP3_AUTH']));
				$auth->setCookie($form['nickname'], isset($new_pwd) ? $new_pwd : $cookie_arr[1], 3600);
			}

			$content = comboBox($bool !== null ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), uri('acp/users'));
		}
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$user = $auth->getUserInfo($uri->id);

		// Zeitzonen
		$tpl->assign('time_zone', timeZones($user['time_zone']));

		// Sommerzeit an/aus
		$dst[0]['value'] = '1';
		$dst[0]['checked'] = selectEntry('dst', '1', $user['dst'], 'checked');
		$dst[0]['lang'] = $lang->t('common', 'yes');
		$dst[1]['value'] = '0';
		$dst[1]['checked'] = selectEntry('dst', '0', $user['dst'], 'checked');
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
				$languages[$name]['selected'] = selectEntry('language', $lang_dir[$i], $db->escape($user['language'], 3));
				$languages[$name]['name'] = $lang_info['name'];
			}
		}
		ksort($languages);
		$tpl->assign('languages', $languages);

		// Zugriffslevel holen
		$access = $db->select('id, name', 'access', 0, 'name ASC');
		$c_access = count($access);
		for ($i = 0; $i < $c_access; ++$i) {
			$access[$i]['name'] = $access[$i]['name'];
			$access[$i]['selected'] = selectEntry('access', $access[$i]['id'], $user['access']);
		}
		$tpl->assign('access', $access);

		$tpl->assign('form', isset($form) ? $form : $user);

		$content = $tpl->fetch('users/edit.html');
	}
} else {
	redirect('errors/404');
}
?>