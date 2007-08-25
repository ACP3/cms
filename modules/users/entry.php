<?php
/**
 * Users
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3') && !defined('IN_ADM'))
	exit;
if (!$modules->check('users', 'entry'))
	redirect('errors/403');

switch ($modules->action) {
	case 'create':
		$form = $_POST['form'];

		if (empty($form['nickname']))
			$errors[] = lang('common', 'name_to_short');
		if (!empty($form['nickname']) && $db->select('id', 'users', 'nickname = \'' . $db->escape($form['nickname']) . '\'', 0, 0, 0, 1) == '1')
			$errors[] = lang('users', 'user_name_already_exists');
		if (!$validate->email($form['mail']))
			$errors[] = lang('common', 'wrong_email_format');
		if ($validate->email($form['mail']) && $db->select('id', 'users', 'mail =\'' . $form['mail'] . '\'', 0, 0, 0, 1) > 0)
			$errors[] = lang('common', 'user_email_already_exists');
		if (!ereg('[0-9]', $form['access']))
			$errors[] = lang('users', 'select_access_level');
		if (empty($form['pwd']) || empty($form['pwd_repeat']) || $form['pwd'] != $form['pwd_repeat'])
			$errors[] = lang('users', 'type_in_pwd');

		if (isset($errors)) {
			combo_box($errors);
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
		break;
	case 'edit':
		$form = $_POST['form'];

		if (empty($form['nickname']))
			$errors[] = lang('common', 'name_to_short');
		if (!empty($form['nickname']) && $db->select('id', 'users', 'id != \'' . $modules->id . '\' AND nickname = \'' . $db->escape($form['nickname']) . '\'', 0, 0, 0, 1) == '1')
			$errors[] = lang('users', 'user_name_already_exists');
		if (!$validate->email($form['mail']))
			$errors[] = lang('common', 'wrong_email_format');
		if ($validate->email($form['mail']) && $db->select('id', 'users', 'id != \'' . $modules->id . '\' AND mail =\'' . $form['mail'] . '\'', 0, 0, 0, 1) > 0)
			$errors[] = lang('common', 'user_email_already_exists');
		if (!ereg('[0-9]', $form['access']))
			$errors[] = lang('users', 'select_access_level');
		if (!empty($form['new_pwd']) && !empty($form['new_pwd_repeat']) && $form['new_pwd'] != $form['new_pwd_repeat'])
			$errors[] = lang('users', 'type_in_pwd');

		if (isset($errors)) {
			combo_box($errors);
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

			$bool = $db->update('users', $update_values, 'id = \'' . $modules->id . '\'');

			// Falls sich der User selbst bearbeitet hat, Cookies und Session aktualisieren
			if ($modules->id == $_SESSION['acp3_id']) {
				$cookie_arr = explode('|', $_COOKIE['ACP3_AUTH']);
				setcookie('ACP3_AUTH', $form['nickname'] . '|' . (isset($new_pwd) ? $new_pwd : $cookie_arr[1]), time() + 3600, ROOT_DIR);

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
			$bool = false;
			$admin_user = false;
			$session_user = false;
			foreach ($marked_entries as $entry) {
				if (!empty($entry) && ereg('[0-9]', $entry) && $db->select('id', 'users', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1') {
					if ($entry == '1') {
						$admin_user = true;
					} else {
						if ($entry == $_SESSION['acp3_id']) {
							$session_user = true;
						}
						$bool = $db->delete('users', 'id = \'' . $entry . '\'');
					}
				}
			}
			// Falls sich der User selbst gelöscht hat, diesen auch gleich abmelden
			if ($session_user) {
				if (isset($_COOKIE[session_name()])) {
					setcookie(session_name(), '', time() - 3600, ROOT_DIR);
				}
				setcookie('ACP3_AUTH', '', time() - 3600, ROOT_DIR);

				$_SESSION = array();

				session_destroy();
				$check_admin = true;
			}
			if ($admin_user) {
				$text = lang('users', 'admin_user_undeletable');
			} else {
				$text = $bool ? lang('users', 'delete_success') : lang('users', 'delete_error');
			}
			$content = combo_box($text, $session_user ? ROOT_DIR : uri('acp/users'));
		} else {
			redirect('errors/404');
		}
		break;
	case 'edit_profile':
		if (!$auth->is_user() || !preg_match('/\d/', $_SESSION['acp3_id'])) {
			redirect('errors/403');
		} else {
			$form = $_POST['form'];

			if (empty($form['nickname']))
				$errors[] = lang('common', 'name_to_short');
			if (!empty($form['nickname']) && $db->select('id', 'users', 'id != \'' . $_SESSION['acp3_id'] . '\' AND nickname = \'' . $db->escape($form['nickname']) . '\'', 0, 0, 0, 1) == '1')
				$errors[] = lang('users', 'user_name_already_exists');
			if (!$validate->email($form['mail']))
				$errors[] = lang('common', 'wrong_email_format');
			if ($validate->email($form['mail']) && $db->select('id', 'users', 'id != \'' . $_SESSION['acp3_id'] . '\' AND mail =\'' . $form['mail'] . '\'', 0, 0, 0, 1) > 0)
				$errors[] = lang('common', 'user_email_already_exists');
			if (!empty($form['new_pwd']) && !empty($form['new_pwd_repeat']) && $form['new_pwd'] != $form['new_pwd_repeat'])
				$errors[] = lang('users', 'type_in_pwd');

			if (isset($errors)) {
				combo_box($errors);
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
					'mail' => $form['mail'],
					'website' => $db->escape($form['website'], 2),
				);
				if (is_array($new_pwd_sql)) {
					$update_values = array_merge($update_values, $new_pwd_sql);
				}

				$bool = $db->update('users', $update_values, 'id = \'' . $_SESSION['acp3_id'] . '\'');

				$cookie_arr = explode('|', $_COOKIE['ACP3_AUTH']);
				setcookie('ACP3_AUTH', $form['nickname'] . '|' . (isset($new_pwd) ? $new_pwd : $cookie_arr[1]), time() + 3600, ROOT_DIR);

				$content = combo_box($bool ? lang('users', 'edit_profile_success') : lang('users', 'edit_profile_error'), uri('users/home'));
			}
		}
		break;
	case 'edit_settings':
		if (!$auth->is_user() || !preg_match('/\d/', $_SESSION['acp3_id'])) {
			redirect('errors/403');
		} else {
			$form = $_POST['form'];

			if (!ereg('[0-9]', $form['time_zone']))
				$errors[] = lang('common', 'select_time_zone');
			if (!ereg('[0-9]', $form['dst']))
				$errors[] = lang('common', 'select_daylight_saving_time');
			if (!is_file('languages/' . $db->escape($form['language'], 2) . '/info.php'))
				$errors[] = lang('users', 'select_language');

			if (isset($errors)) {
				combo_box($errors);
			} else {
				$update_values = array(
					'time_zone' => $form['time_zone'],
					'dst' => $form['dst'],
					'language' => $db->escape($form['language'], 2),
				);

				$bool = $db->update('users', $update_values, 'id = \'' . $_SESSION['acp3_id'] . '\'');

				$content = combo_box($bool ? lang('users', 'edit_settings_success') : lang('users', 'edit_settings_error'), uri('users/home'));
			}
		}
		break;
	case 'forgot_pwd':
		$form = $_POST['form'];

		if (empty($form['nickname']) && empty($form['mail']))
			$errors[] = lang('users', 'type_in_nickname_or_email');
		if (!empty($form['nickname']) && $db->select('id', 'users', 'nickname = \'' . $db->escape($form['nickname']) . '\'', 0, 0, 0, 1) == '0')
			$errors[] = lang('users', 'user_not_exists');
		if (!empty($form['mail']) && !$validate->email($form['mail']))
			$errors[] = lang('common', 'wrong_email_format');
		if ($validate->email($form['mail']) && $db->select('id', 'users', 'mail = \'' . $form['mail'] . '\'', 0, 0, 0, 1) == '0')
			$errors[] = lang('users', 'user_not_exists');

		if (isset($errors)) {
			combo_box($errors);
		} else {
			// Neues Passwort erstellen und neuen Zufallsschlüssel erstellen
			$new_password = salt(8);
			$salt = salt(12);

			// Je nachdem welches Feld ausgefüllt wurde, dieses auswählen
			$where_stmt = !empty($form['mail']) ? 'mail = \'' . $form['mail'] . '\'' : 'nickname = \'' . $db->escape($form['nickname']) . '\'';
			$user = $db->select('id, name, mail', 'users', $where_stmt);

			// E-Mail mit dem neuen Passwort versenden
			$subject = sprintf(lang('users', 'forgot_pwd_mail_subject'), CONFIG_TITLE, htmlentities($_SERVER['HTTP_HOST']));
			$message = sprintf(lang('users', 'forgot_pwd_mail_message'), $user[0]['nickname'], CONFIG_TITLE, htmlentities($_SERVER['HTTP_HOST']), $user[0]['mail'], $new_password);
			$header = 'Content-type: text/plain; charset=' . CHARSET;
			$mail_sent = @mail($user[0]['mail'], $subject, $message, $header);

			// Das Passwort des Benutzers nur abändern, wenn die E-Mail erfolgreich versandt werden konnte
			if ($mail_sent) {
				$update_values = array(
					'pwd' => sha1($salt . sha1($new_password)) . ':' . $salt,
				);

				$bool = $db->update('users', $update_values, 'id = \'' . $user[0]['id'] . '\'');
			}
			$content = combo_box($mail_sent && isset($bool) && $bool ? lang('users', 'forgot_pwd_success') : lang('users', 'forgot_pwd_error'), ROOT_DIR);
		}
		break;
	case 'home':
		if (!$auth->is_user() || !preg_match('/\d/', $_SESSION['acp3_id'])) {
			redirect('errors/403');
		} else {
			$form = $_POST['form'];

			$bool = $db->update('users', array('draft' => $db->escape($form['draft'], 2)), 'id = \'' . $_SESSION['acp3_id'] . '\'');

			$content = combo_box($bool ? lang('users', 'draft_success') : lang('users', 'draft_error'), uri('users/home'));
		}
		break;
	case 'register':
		$form = $_POST['form'];

		if (empty($form['nickname']))
			$errors[] = lang('common', 'name_to_short');
		if (!empty($form['nickname']) && $db->select('id', 'users', 'nickname = \'' . $db->escape($form['nickname']) . '\'', 0, 0, 0, 1) == '1')
			$errors[] = lang('users', 'user_name_already_exists');
		if (!$validate->email($form['mail']))
			$errors[] = lang('common', 'wrong_email_format');
		if ($validate->email($form['mail']) && $db->select('id', 'users', 'mail =\'' . $form['mail'] . '\'', 0, 0, 0, 1) > 0)
			$errors[] = lang('common', 'user_email_already_exists');
		if (empty($form['pwd']) || empty($form['pwd_repeat']) || $form['pwd'] != $form['pwd_repeat'])
			$errors[] = lang('users', 'type_in_pwd');

		if (isset($errors)) {
			combo_box($errors);
		} else {
			$salt = salt(12);

			// E-Mail mit den Accountdaten zusenden
			$subject = sprintf(lang('users', 'register_mail_subject'), CONFIG_TITLE, htmlentities($_SERVER['HTTP_HOST']));
			$message = sprintf(lang('users', 'register_mail_message'), $db->escape($form['nickname']), CONFIG_TITLE, htmlentities($_SERVER['HTTP_HOST']), $form['mail'], $form['pwd']);
			$header = 'Content-type: text/plain; charset=' . CHARSET;
			$mail_sent = @mail($form['mail'], $subject, $message, $header);

			// Das Benutzerkonto nur erstellen, wenn die E-Mail erfolgreich versandt werden konnte
			if ($mail_sent) {
				$insert_values = array(
					'id' => '',
					'nickname' => $db->escape($form['nickname']),
					'realname' => '',
					'pwd' => sha1($salt . sha1($form['pwd'])) . ':' . $salt,
					'access' => '3',
					'mail' => $form['mail'],
					'website' => '',
					'time_zone' => CONFIG_TIME_ZONE,
					'dst' => CONFIG_DST,
					'language' => CONFIG_LANG,
					'draft' => '',
				);

				$bool = $db->insert('users', $insert_values);
			}

			$content = combo_box($mail_sent && isset($bool) && $bool ? lang('users', 'register_success') : lang('users', 'register_error'), ROOT_DIR);
		}
		break;
	default:
		redirect('errors/404');
}
?>