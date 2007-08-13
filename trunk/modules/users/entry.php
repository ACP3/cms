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
if (!$modules->check())
	redirect('errors/403');
switch ($modules->action) {
	case 'create':
		$form = $_POST['form'];
		$i = 0;

		if (empty($form['name']))
			$errors[$i++] = lang('common', 'name_to_short');
		if (!empty($form['name']) && $db->select('id', 'users', "name='" . $db->escape($form['name']) . "'", 0, 0, 0, 1) == '1')
			$errors[$i++] = lang('users', 'user_already_exists');
		if (!$validate->email($form['mail']))
			$errors[$i++] = lang('common', 'wrong_email_format');
		if (!ereg('[0-9]', $form['access']))
			$errors[$i++] = lang('users', 'select_access_level');
		if (empty($form['pwd']) || empty($form['pwd_repeat']) || $form['pwd'] != $form['pwd_repeat'])
			$errors[$i++] = lang('users', 'type_in_pwd');

		if (isset($errors)) {
			$error_msg = combo_box($errors);
		} else {
			$salt = salt(12);

			$insert_values = array(
				'id' => '',
				'name' => $db->escape($form['name']),
				'pwd' => sha1($salt . sha1($form['pwd'])) . ':' . $salt,
				'access' => $form['access'],
				'mail' => $form['mail'],
			);

			$bool = $db->insert('users', $insert_values);

			$content = combo_box($bool ? lang('users', 'create_success') : lang('users', 'create_error'), uri('acp/users'));
		}
		break;
	case 'edit':
		$form = $_POST['form'];
		$i = 0;

		if (empty($form['name']))
			$errors[$i++] = lang('common', 'name_to_short');
		if (!empty($form['name']) && $db->select('id', 'users', "id != '" . $modules->id . "' AND name='" . $db->escape($form['name']) . "'", 0, 0, 0, 1) == '1')
			$errors[$i++] = lang('users', 'user_already_exists');
		if (!$validate->email($form['mail']))
			$errors[$i++] = lang('common', 'wrong_email_format');
		if (!ereg('[0-9]', $form['access']))
			$errors[$i++] = lang('users', 'select_access_level');
		if (!empty($form['new_pwd']) && !empty($form['new_pwd_repeat']) && $form['new_pwd'] != $form['new_pwd_repeat'])
			$errors[$i++] = lang('users', 'type_in_pwd');

		if (isset($errors)) {
			$error_msg = combo_box($errors);
		} else {
			$new_pwd_sql = null;
			//Neues Passwort
			if (!empty($form['new_pwd']) && !empty($form['new_pwd_repeat'])) {
				$salt = salt(12);
				$pwd = sha1($salt . sha1($form['new_pwd']));
				$new_pwd = array('pwd' => $pwd . ':' . $salt,);
			}

			$update_values = array(
				'name' => $db->escape($form['name']),
				'access' => $form['access'],
				'mail' => $form['mail'],
			);
			if (is_array($new_pwd_sql)) {
				$update_values = array_merge($update_values, $new_pwd_sql);
			}

			$bool = $db->update('users', $update_values, 'id = \'' . $modules->id . '\'');

			// Falls sich der User selbst bearbeitet hat, Cookies und Session aktualisieren
			if ($modules->id == $_SESSION['acp3_id']) {
				$cookie_arr = explode('|', $_COOKIE['ACP3_AUTH']);
				setcookie('ACP3_AUTH', $form['name'] . '|' . (isset($pwd) ? $pwd : $cookie_arr[1]), time() + 3600, '/');

				$_SESSION['acp3_access'] = $form['access'];
			}

			$content = combo_box($bool ? lang('users', 'edit_success') : lang('users', 'edit_error'), uri('acp/users'));
		}
		break;
	case 'delete':
		if (isset($_POST['entries']) && is_array($_POST['entries']))
			$entries = $_POST['entries'];
		elseif (isset($modules->gen['entries']) && ereg('^([0-9|]+)$', $modules->gen['entries']))
			$entries = $modules->gen['entries'];

		if (is_array($entries)) {
			$marked_entries = '';
			foreach ($entries as $entry) {
				$marked_entries.= $entry . '|';
			}
			$content = combo_box(lang('users', 'confirm_delete'), uri('acp/users/adm_list/action_delete/entries_' . $marked_entries), uri('acp/users'));
		} elseif (ereg('^([0-9|]+)$', $entries) && isset($modules->gen['confirmed'])) {
			$marked_entries = explode('|', $entries);
			$bool = 0;
			$s_user = 0;
			foreach ($marked_entries as $entry) {
				if (!empty($entry) && ereg('[0-9]', $entry) && $db->select('id', 'users', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1') {
					if ($entry == '1') {
						$admin_user = 1;
						break;
					} elseif ($entry == $_SESSION['acp3_id']) {
						$s_user = 1;
					} else {
						$bool = $db->delete('users', 'id = \'' . $entry . '\'');
					}
				}
			}
			// Falls sich der User selbst gelöscht hat, diesen auch gleich abmelden
			if ($s_user) {
				if (isset($_COOKIE[session_name()])) {
					setcookie(session_name(), '', time() - 3600, '/');
				}
				setcookie('ACP3_AUTH', '', time() - 3600, '/');

				$_SESSION = array();

				session_destroy();
				$check_admin = true;
			}
			if ($bool) {
				$text = lang('users', 'delete_success');
			} elseif ($admin_user) {
				$text = lang('users', 'admin_user_undeletable');
			} else {
				$text = lang('users', 'delete_error');
			}
			$content = combo_box($text, $s_user ? ROOT_DIR : uri('acp/users'));
		} else {
			redirect('errors/404');
		}
		break;
	default:
		redirect('errors/404');
}
?>