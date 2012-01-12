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

if (validate::isNumber($uri->id) && $db->countRows('*', 'users', 'id = \'' . $uri->id . '\'') == '1') {
	$user = $auth->getUserInfo($uri->id);

	if (isset($_POST['form'])) {
		require_once MODULES_DIR . 'users/functions.php';

		$form = $_POST['form'];

		if (empty($form['nickname']))
			$errors[] = $lang->t('common', 'name_to_short');
		if (!validate::email($form['mail']))
			$errors[] = $lang->t('common', 'wrong_email_format');
		if (userEmailExists($form['mail'], $uri->id))
			$errors[] = $lang->t('users', 'user_email_already_exists');
		if (userNameExists($form['nickname'], $uri->id))
			$errors[] = $lang->t('users', 'user_name_already_exists');
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
		if (!empty($form['new_pwd']) && !empty($form['new_pwd_repeat']) && $form['new_pwd'] != $form['new_pwd_repeat'])
			$errors[] = $lang->t('users', 'type_in_pwd');

		if (isset($errors)) {
			$tpl->assign('error_msg', comboBox($errors));
		} else {
			$update_values = array(
				'nickname' => $db->escape($form['nickname']),
				'realname' => $db->escape($form['realname']) . ':' . $user['realname_display'],
				'mail' => $form['mail'] . ':' . $user['mail_display'],
				'website' => $db->escape($form['website'], 2) . ':' . $user['website_display'],
				'date_format_long' => $db->escape($form['date_format_long']),
				'date_format_short' => $db->escape($form['date_format_short']),
				'time_zone' => $form['time_zone'],
				'dst' => $form['dst'],
				'language' => $db->escape($form['language'], 2),
				'entries' => (int) $form['entries'],
			);

			// Rollen aktualisieren
			$db->link->beginTransaction();
			$db->delete('acl_user_roles', 'user_id = \'' . $uri->id . '\'');
			foreach ($form['roles'] as $row) {
				$db->insert('acl_user_roles', array('user_id' => $uri->id, 'role_id' => $row));
			}
			$db->link->commit();

			// Neues Passwort
			if (!empty($form['new_pwd']) && !empty($form['new_pwd_repeat'])) {
				$salt = salt(12);
				$new_pwd = genSaltedPassword($salt, $form['new_pwd']);
				$update_values['pwd'] = $new_pwd . ':' . $salt;
			}

			$bool = $db->update('users', $update_values, 'id = \'' . $uri->id . '\'');

			// Falls sich der User selbst bearbeitet hat, Cookie aktualisieren
			if ($uri->id == $auth->getUserId()) {
				$cookie_arr = explode('|', base64_decode($_COOKIE['ACP3_AUTH']));
				$auth->setCookie($form['nickname'], isset($new_pwd) ? $new_pwd : $cookie_arr[1], 3600);
			}

			$content = comboBox($bool !== null ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), $uri->route('acp/users'));
		}
	}
	if (!isset($_POST['form']) || isset($errors) && is_array($errors)) {
		$user['nickname'] = $db->escape($user['nickname'], 3);
		$user['date_format_long'] = $db->escape($user['date_format_long'], 3);
		$user['date_format_short'] = $db->escape($user['date_format_short'], 3);

		// Zugriffslevel holen
		$roles = acl::getAllRoles();
		$c_roles = count($roles);
		$user_roles = acl::getUserRoles($uri->id);
		for ($i = 0; $i < $c_roles; ++$i) {
			$roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
			$roles[$i]['selected'] = selectEntry('roles', $roles[$i]['id'], in_array($roles[$i]['id'], $user_roles) ? $roles[$i]['id'] : '');
		}
		$tpl->assign('roles', $roles);

		// Sprache
		$user['language'] = $db->escape($user['language'], 3);
		$languages = array();
		$lang_dir = scandir(ACP3_ROOT . 'languages');
		$c_lang_dir = count($lang_dir);
		for ($i = 0; $i < $c_lang_dir; ++$i) {
			$lang_info = xml::parseXmlFile(ACP3_ROOT . 'languages/' . $lang_dir[$i] . '/info.xml', '/language');
			if (!empty($lang_info)) {
				$name = $lang_info['name'];
				$languages[$name]['dir'] = $lang_dir[$i];
				$languages[$name]['selected'] = selectEntry('language', $lang_dir[$i], $user['language']);
				$languages[$name]['name'] = $lang_info['name'];
			}
		}
		ksort($languages);
		$tpl->assign('languages', $languages);

		// Einträge pro Seite
		for ($i = 0, $j = 10; $j <= 50; $i++, $j = $j + 10) {
			$entries[$i]['value'] = $j;
			$entries[$i]['selected'] = selectEntry('entries', $j, $auth->entries);
		}
		$tpl->assign('entries', $entries);

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

		$tpl->assign('form', isset($form) ? $form : $user);

		$content = modules::fetchTemplate('users/edit.tpl');
	}
} else {
	$uri->redirect('errors/404');
}
