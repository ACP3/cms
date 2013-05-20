<?php

namespace ACP3\Modules\Users;

use ACP3\Core;

/**
 * Description of UsersAdmin
 *
 * @author Tino
 */
class UsersAdmin extends Core\ModuleController {

	public function actionCreate()
	{
		if (isset($_POST['submit']) === true) {
			if (empty($_POST['nickname']))
				$errors['nickname'] = Core\Registry::get('Lang')->t('system', 'name_to_short');
			if (Core\Validate::gender($_POST['gender']) === false)
				$errors['gender'] = Core\Registry::get('Lang')->t('users', 'select_gender');
			if (!empty($_POST['birthday']) && Core\Validate::birthday($_POST['birthday']) === false)
				$errors[] = Core\Registry::get('Lang')->t('users', 'invalid_birthday');
			if (UsersFunctions::userNameExists($_POST['nickname']) === true)
				$errors['nickname'] = Core\Registry::get('Lang')->t('users', 'user_name_already_exists');
			if (Core\Validate::email($_POST['mail']) === false)
				$errors['mail'] = Core\Registry::get('Lang')->t('system', 'wrong_email_format');
			if (UsersFunctions::userEmailExists($_POST['mail']) === true)
				$errors['mail'] = Core\Registry::get('Lang')->t('users', 'user_email_already_exists');
			if (empty($_POST['roles']) || is_array($_POST['roles']) === false || Core\Validate::aclRolesExist($_POST['roles']) === false)
				$errors['roles'] = Core\Registry::get('Lang')->t('users', 'select_access_level');
			if (!isset($_POST['super_user']) || ($_POST['super_user'] != 1 && $_POST['super_user'] != 0))
				$errors['super-user'] = Core\Registry::get('Lang')->t('users', 'select_super_user');
			if (Core\Registry::get('Lang')->languagePackExists($_POST['language']) === false)
				$errors['language'] = Core\Registry::get('Lang')->t('users', 'select_language');
			if (Core\Validate::isNumber($_POST['entries']) === false)
				$errors['entries'] = Core\Registry::get('Lang')->t('system', 'select_records_per_page');
			if (empty($_POST['date_format_long']) || empty($_POST['date_format_short']))
				$errors[] = Core\Registry::get('Lang')->t('system', 'type_in_date_format');
			if (Core\Validate::timeZone($_POST['date_time_zone']) === false)
				$errors['time-zone'] = Core\Registry::get('Lang')->t('system', 'select_time_zone');
			if (!empty($_POST['icq']) && Core\Validate::icq($_POST['icq']) === false)
				$errors['icq'] = Core\Registry::get('Lang')->t('users', 'invalid_icq_number');
			if (in_array($_POST['mail_display'], array(0, 1)) === false)
				$errors[] = Core\Registry::get('Lang')->t('users', 'select_mail_display');
			if (in_array($_POST['address_display'], array(0, 1)) === false)
				$errors[] = Core\Registry::get('Lang')->t('users', 'select_address_display');
			if (in_array($_POST['country_display'], array(0, 1)) === false)
				$errors[] = Core\Registry::get('Lang')->t('users', 'select_country_display');
			if (in_array($_POST['birthday_display'], array(0, 1, 2)) === false)
				$errors[] = Core\Registry::get('Lang')->t('users', 'select_birthday_display');
			if (empty($_POST['pwd']) || empty($_POST['pwd_repeat']) || $_POST['pwd'] != $_POST['pwd_repeat'])
				$errors[] = Core\Registry::get('Lang')->t('users', 'type_in_pwd');

			if (isset($errors) === true) {
				Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
			} else {
				$salt = salt(12);

				$insert_values = array(
					'id' => '',
					'super_user' => (int) $_POST['super_user'],
					'nickname' => Core\Functions::str_encode($_POST['nickname']),
					'pwd' => generateSaltedPassword($salt, $_POST['pwd']) . ':' . $salt,
					'realname' => Core\Functions::str_encode($_POST['realname']),
					'gender' => (int) $_POST['gender'],
					'birthday' => $_POST['birthday'],
					'birthday_display' => (int) $_POST['birthday_display'],
					'mail' => $_POST['mail'],
					'mail_display' => isset($_POST['mail_display']) ? 1 : 0,
					'website' => Core\Functions::str_encode($_POST['website']),
					'icq' => $_POST['icq'],
					'skype' => Core\Functions::str_encode($_POST['skype']),
					'street' => Core\Functions::str_encode($_POST['street']),
					'house_number' => Core\Functions::str_encode($_POST['house_number']),
					'zip' => Core\Functions::str_encode($_POST['zip']),
					'city' => Core\Functions::str_encode($_POST['city']),
					'address_display' => isset($_POST['address_display']) ? 1 : 0,
					'country' => Core\Functions::str_encode($_POST['country']),
					'country_display' => isset($_POST['country_display']) ? 1 : 0,
					'date_format_long' => Core\Functions::str_encode($_POST['date_format_long']),
					'date_format_short' => Core\Functions::str_encode($_POST['date_format_short']),
					'time_zone' => $_POST['date_time_zone'],
					'language' => $_POST['language'],
					'entries' => (int) $_POST['entries'],
					'draft' => '',
				);

				Core\Registry::get('Db')->beginTransaction();
				try {
					$bool = Core\Registry::get('Db')->insert(DB_PRE . 'users', $insert_values);
					$user_id = Core\Registry::get('Db')->lastInsertId();
					foreach ($_POST['roles'] as $row) {
						Core\Registry::get('Db')->insert(DB_PRE . 'acl_user_roles', array('user_id' => $user_id, 'role_id' => $row));
					}
					Core\Registry::get('Db')->commit();
				} catch (\Exception $e) {
					Core\Registry::get('Db')->rollback();
					$bool = false;
				}

				Core\Registry::get('Session')->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/users');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			// Zugriffslevel holen
			$roles = Core\ACL::getAllRoles();
			$c_roles = count($roles);
			for ($i = 0; $i < $c_roles; ++$i) {
				$roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
				$roles[$i]['selected'] = Core\Functions::selectEntry('roles', $roles[$i]['id']);
			}
			Core\Registry::get('View')->assign('roles', $roles);

			// Super User
			$lang_super_user = array(Core\Registry::get('Lang')->t('system', 'yes'), Core\Registry::get('Lang')->t('system', 'no'));
			Core\Registry::get('View')->assign('super_user', Core\Functions::selectGenerator('super_user', array(1, 0), $lang_super_user, 0, 'checked'));

			// Sprache
			$languages = array();
			$lang_dir = scandir(ACP3_ROOT_DIR . 'languages');
			$c_lang_dir = count($lang_dir);
			for ($i = 0; $i < $c_lang_dir; ++$i) {
				$lang_info = Core\XML::parseXmlFile(ACP3_ROOT_DIR . 'languages/' . $lang_dir[$i] . '/info.xml', '/language');
				if (!empty($lang_info)) {
					$name = $lang_info['name'];
					$languages[$name]['dir'] = $lang_dir[$i];
					$languages[$name]['selected'] = Core\Functions::selectEntry('language', $lang_dir[$i], CONFIG_LANG);
					$languages[$name]['name'] = $lang_info['name'];
				}
			}
			ksort($languages);
			Core\Registry::get('View')->assign('languages', $languages);

			// Einträge pro Seite
			Core\Registry::get('View')->assign('entries', Core\Functions::recordsPerPage(CONFIG_ENTRIES));

			// Zeitzonen
			Core\Registry::get('View')->assign('time_zones', Core\Registry::get('Date')->getTimeZones(CONFIG_DATE_TIME_ZONE));

			// Geschlecht
			$lang_gender = array(
				Core\Registry::get('Lang')->t('users', 'gender_not_specified'),
				Core\Registry::get('Lang')->t('users', 'gender_female'),
				Core\Registry::get('Lang')->t('users', 'gender_male')
			);
			Core\Registry::get('View')->assign('gender', Core\Functions::selectGenerator('gender', array(1, 2, 3), $lang_gender, ''));

			// Geburtstag
			Core\Registry::get('View')->assign('birthday_datepicker', Core\Registry::get('Date')->datepicker('birthday', '', 'Y-m-d', array('constrainInput' => 'true', 'changeMonth' => 'true', 'changeYear' => 'true', 'yearRange' => '\'-50:+0\''), 0, 1, false, true));

			// Kontaktangaben
			$contact = array();
			$contact[0]['name'] = 'mail';
			$contact[0]['lang'] = Core\Registry::get('Lang')->t('system', 'email_address');
			$contact[0]['value'] = isset($_POST['submit']) ? $_POST['mail'] : '';
			$contact[0]['maxlength'] = '120';
			$contact[1]['name'] = 'website';
			$contact[1]['lang'] = Core\Registry::get('Lang')->t('system', 'website');
			$contact[1]['value'] = isset($_POST['submit']) ? $_POST['website'] : '';
			$contact[1]['maxlength'] = '120';
			$contact[2]['name'] = 'icq';
			$contact[2]['lang'] = Core\Registry::get('Lang')->t('users', 'icq');
			$contact[2]['value'] = isset($_POST['submit']) ? $_POST['icq'] : '';
			$contact[2]['maxlength'] = '9';
			$contact[3]['name'] = 'skype';
			$contact[3]['lang'] = Core\Registry::get('Lang')->t('users', 'skype');
			$contact[3]['value'] = isset($_POST['submit']) ? $_POST['skype'] : '';
			$contact[3]['maxlength'] = '28';
			Core\Registry::get('View')->assign('contact', $contact);

			$countries = Core\Lang::worldCountries();
			$countries_select = array();
			foreach ($countries as $key => $value) {
				$countries_select[] = array(
					'value' => $key,
					'lang' => $value,
					'selected' => Core\Functions::selectEntry('countries', $key),
				);
			}
			Core\Registry::get('View')->assign('countries', $countries_select);

			$lang_mail_display = array(Core\Registry::get('Lang')->t('system', 'yes'), Core\Registry::get('Lang')->t('system', 'no'));
			Core\Registry::get('View')->assign('mail_display', Core\Functions::selectGenerator('mail_display', array(1, 0), $lang_mail_display, 0, 'checked'));

			$lang_address_display = array(Core\Registry::get('Lang')->t('system', 'yes'), Core\Registry::get('Lang')->t('system', 'no'));
			Core\Registry::get('View')->assign('address_display', Core\Functions::selectGenerator('address_display', array(1, 0), $lang_address_display, 0, 'checked'));

			$lang_country_display = array(Core\Registry::get('Lang')->t('system', 'yes'), Core\Registry::get('Lang')->t('system', 'no'));
			Core\Registry::get('View')->assign('country_display', Core\Functions::selectGenerator('country_display', array(1, 0), $lang_country_display, 0, 'checked'));

			$lang_birthday_display = array(
				Core\Registry::get('Lang')->t('users', 'birthday_hide'),
				Core\Registry::get('Lang')->t('users', 'birthday_display_completely'),
				Core\Registry::get('Lang')->t('users', 'birthday_hide_year')
			);
			Core\Registry::get('View')->assign('birthday_display', Core\Functions::selectGenerator('birthday_display', array(0, 1, 2), $lang_birthday_display, 0, 'checked'));

			$defaults = array(
				'nickname' => '',
				'realname' => '',
				'mail' => '',
				'website' => '',
				'street' => '',
				'house_number' => '',
				'zip' => '',
				'city' => '',
				'date_format_long' => CONFIG_DATE_FORMAT_LONG,
				'date_format_short' => CONFIG_DATE_FORMAT_SHORT
			);

			Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : $defaults);

			Core\Registry::get('Session')->generateFormToken();
		}
	}

	public function actionDelete()
	{
		if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
			$entries = $_POST['entries'];
		elseif (Core\Validate::deleteEntries(Core\Registry::get('URI')->entries) === true)
			$entries = Core\Registry::get('URI')->entries;

		if (!isset($entries)) {
			Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'no_entries_selected')));
		} elseif (is_array($entries) === true) {
			$marked_entries = implode('|', $entries);
			Core\Registry::get('View')->setContent(Core\Functions::confirmBox(Core\Registry::get('Lang')->t('system', 'confirm_delete'), Core\Registry::get('URI')->route('acp/users/delete/entries_' . $marked_entries . '/action_confirmed/'), Core\Registry::get('URI')->route('acp/users')));
		} elseif (Core\Registry::get('URI')->action === 'confirmed') {
			$marked_entries = explode('|', $entries);
			$bool = false;
			$admin_user = false;
			$self_delete = false;
			foreach ($marked_entries as $entry) {
				if ($entry == 1) {
					$admin_user = true;
				} else {
					// Falls sich der User selbst gelöscht hat, diesen auch gleich abmelden
					if ($entry == Core\Registry::get('Auth')->getUserId()) {
						Core\Registry::get('Auth')->logout();
						$self_delete = true;
					}
					$bool = Core\Registry::get('Db')->delete(DB_PRE . 'users', array('id' => $entry));
				}
			}
			if ($admin_user === true) {
				$bool = false;
				$text = Core\Registry::get('Lang')->t('users', 'admin_user_undeletable');
			} else {
				$text = Core\Registry::get('Lang')->t('system', $bool !== false ? 'delete_success' : 'delete_error');
			}
			Core\Functions::setRedirectMessage($bool, $text, $self_delete === true ? ROOT_DIR : 'acp/users');
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionEdit()
	{
		if (Core\Validate::isNumber(Core\Registry::get('URI')->id) === true &&
				Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'users WHERE id = ?', array(Core\Registry::get('URI')->id)) == 1) {
			$user = Core\Registry::get('Auth')->getUserInfo(Core\Registry::get('URI')->id);

			if (isset($_POST['submit']) === true) {
				if (empty($_POST['nickname']))
					$errors['nickname'] = Core\Registry::get('Lang')->t('system', 'name_to_short');
				if (Core\Validate::gender($_POST['gender']) === false)
					$errors['gender'] = Core\Registry::get('Lang')->t('users', 'select_gender');
				if (!empty($_POST['birthday']) && Core\Validate::birthday($_POST['birthday']) === false)
					$errors[] = Core\Registry::get('Lang')->t('users', 'invalid_birthday');
				if (UsersFunctions::userNameExists($_POST['nickname'], Core\Registry::get('URI')->id))
					$errors['nickname'] = Core\Registry::get('Lang')->t('users', 'user_name_already_exists');
				if (Core\Validate::email($_POST['mail']) === false)
					$errors['mail'] = Core\Registry::get('Lang')->t('system', 'wrong_email_format');
				if (UsersFunctions::userEmailExists($_POST['mail'], Core\Registry::get('URI')->id))
					$errors['mail'] = Core\Registry::get('Lang')->t('users', 'user_email_already_exists');
				if (empty($_POST['roles']) || is_array($_POST['roles']) === false || Core\Validate::aclRolesExist($_POST['roles']) === false)
					$errors['roles'] = Core\Registry::get('Lang')->t('users', 'select_access_level');
				if (!isset($_POST['super_user']) || ($_POST['super_user'] != 1 && $_POST['super_user'] != 0))
					$errors['super-user'] = Core\Registry::get('Lang')->t('users', 'select_super_user');
				if (Core\Registry::get('Lang')->languagePackExists($_POST['language']) === false)
					$errors['language'] = Core\Registry::get('Lang')->t('users', 'select_language');
				if (Core\Validate::isNumber($_POST['entries']) === false)
					$errors['entries'] = Core\Registry::get('Lang')->t('system', 'select_records_per_page');
				if (empty($_POST['date_format_long']) || empty($_POST['date_format_short']))
					$errors[] = Core\Registry::get('Lang')->t('system', 'type_in_date_format');
				if (Core\Validate::timeZone($_POST['date_time_zone']) === false)
					$errors['time-zone'] = Core\Registry::get('Lang')->t('system', 'select_time_zone');
				if (!empty($_POST['icq']) && Core\Validate::icq($_POST['icq']) === false)
					$errors['icq'] = Core\Registry::get('Lang')->t('users', 'invalid_icq_number');
				if (in_array($_POST['mail_display'], array(0, 1)) === false)
					$errors[] = Core\Registry::get('Lang')->t('users', 'select_mail_display');
				if (in_array($_POST['address_display'], array(0, 1)) === false)
					$errors[] = Core\Registry::get('Lang')->t('users', 'select_address_display');
				if (in_array($_POST['country_display'], array(0, 1)) === false)
					$errors[] = Core\Registry::get('Lang')->t('users', 'select_country_display');
				if (in_array($_POST['birthday_display'], array(0, 1, 2)) === false)
					$errors[] = Core\Registry::get('Lang')->t('users', 'select_birthday_display');
				if (!empty($_POST['new_pwd']) && !empty($_POST['new_pwd_repeat']) && $_POST['new_pwd'] != $_POST['new_pwd_repeat'])
					$errors[] = Core\Registry::get('Lang')->t('users', 'type_in_pwd');

				if (isset($errors) === true) {
					Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
				} else {
					$update_values = array(
						'super_user' => (int) $_POST['super_user'],
						'nickname' => Core\Functions::str_encode($_POST['nickname']),
						'realname' => Core\Functions::str_encode($_POST['realname']),
						'gender' => (int) $_POST['gender'],
						'birthday' => $_POST['birthday'],
						'birthday_display' => (int) $_POST['birthday_display'],
						'mail' => $_POST['mail'],
						'mail_display' => (int) $_POST['mail_display'],
						'website' => Core\Functions::str_encode($_POST['website']),
						'icq' => $_POST['icq'],
						'skype' => Core\Functions::str_encode($_POST['skype']),
						'street' => Core\Functions::str_encode($_POST['street']),
						'house_number' => Core\Functions::str_encode($_POST['house_number']),
						'zip' => Core\Functions::str_encode($_POST['zip']),
						'city' => Core\Functions::str_encode($_POST['city']),
						'address_display' => (int) $_POST['address_display'],
						'country' => Core\Functions::str_encode($_POST['country']),
						'country_display' => (int) $_POST['country_display'],
						'date_format_long' => Core\Functions::str_encode($_POST['date_format_long']),
						'date_format_short' => Core\Functions::str_encode($_POST['date_format_short']),
						'time_zone' => $_POST['date_time_zone'],
						'language' => $_POST['language'],
						'entries' => (int) $_POST['entries'],
					);

					// Rollen aktualisieren
					Core\Registry::get('Db')->beginTransaction();
					try {
						Core\Registry::get('Db')->delete(DB_PRE . 'acl_user_roles', array('user_id' => Core\Registry::get('URI')->id));
						foreach ($_POST['roles'] as $row) {
							Core\Registry::get('Db')->insert(DB_PRE . 'acl_user_roles', array('user_id' => Core\Registry::get('URI')->id, 'role_id' => $row));
						}
						Core\Registry::get('Db')->commit();
					} catch (\Exception $e) {
						Core\Registry::get('Db')->rollback();
					}

					// Neues Passwort
					if (!empty($_POST['new_pwd']) && !empty($_POST['new_pwd_repeat'])) {
						$salt = salt(12);
						$new_pwd = generateSaltedPassword($salt, $_POST['new_pwd']);
						$update_values['pwd'] = $new_pwd . ':' . $salt;
					}

					$bool = Core\Registry::get('Db')->update(DB_PRE . 'users', $update_values, array('id' => Core\Registry::get('URI')->id));

					// Falls sich der User selbst bearbeitet hat, Cookie aktualisieren
					if (Core\Registry::get('URI')->id == Core\Registry::get('Auth')->getUserId()) {
						$cookie_arr = explode('|', base64_decode($_COOKIE['ACP3_AUTH']));
						Core\Registry::get('Auth')->setCookie($_POST['nickname'], isset($new_pwd) ? $new_pwd : $cookie_arr[1], 3600);
					}

					Core\Registry::get('Session')->unsetFormToken();

					Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/users');
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				// Zugriffslevel holen
				$roles = Core\ACL::getAllRoles();
				$c_roles = count($roles);
				$user_roles = Core\ACL::getUserRoles(Core\Registry::get('URI')->id);
				for ($i = 0; $i < $c_roles; ++$i) {
					$roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
					$roles[$i]['selected'] = Core\Functions::selectEntry('roles', $roles[$i]['id'], in_array($roles[$i]['id'], $user_roles) ? $roles[$i]['id'] : '');
				}
				Core\Registry::get('View')->assign('roles', $roles);

				// Super User
				$lang_super_user = array(Core\Registry::get('Lang')->t('system', 'yes'), Core\Registry::get('Lang')->t('system', 'no'));
				Core\Registry::get('View')->assign('super_user', Core\Functions::selectGenerator('super_user', array(1, 0), $lang_super_user, $user['super_user'], 'checked'));

				// Sprache
				$languages = array();
				$lang_dir = scandir(ACP3_ROOT_DIR . 'languages');
				$c_lang_dir = count($lang_dir);
				for ($i = 0; $i < $c_lang_dir; ++$i) {
					$lang_info = Core\XML::parseXmlFile(ACP3_ROOT_DIR . 'languages/' . $lang_dir[$i] . '/info.xml', '/language');
					if (!empty($lang_info)) {
						$name = $lang_info['name'];
						$languages[$name]['dir'] = $lang_dir[$i];
						$languages[$name]['selected'] = Core\Functions::selectEntry('language', $lang_dir[$i], $user['language']);
						$languages[$name]['name'] = $lang_info['name'];
					}
				}
				ksort($languages);
				Core\Registry::get('View')->assign('languages', $languages);

				// Einträge pro Seite
				Core\Registry::get('View')->assign('entries', Core\Functions::recordsPerPage((int) $user['entries']));

				// Zeitzonen
				Core\Registry::get('View')->assign('time_zones', Core\Date::getTimeZones($user['time_zone']));

				// Geschlecht
				$lang_gender = array(
					Core\Registry::get('Lang')->t('users', 'gender_not_specified'),
					Core\Registry::get('Lang')->t('users', 'gender_female'),
					Core\Registry::get('Lang')->t('users', 'gender_male')
				);
				Core\Registry::get('View')->assign('gender', Core\Functions::selectGenerator('gender', array(1, 2, 3), $lang_gender, $user['gender']));

				// Geburtstag
				Core\Registry::get('View')->assign('birthday_datepicker', Core\Registry::get('Date')->datepicker('birthday', $user['birthday'], 'Y-m-d', array('constrainInput' => 'true', 'changeMonth' => 'true', 'changeYear' => 'true', 'yearRange' => '\'-50:+0\''), 0, 1, false, true));

				// Kontaktangaben
				$contact = array();
				$contact[0]['name'] = 'mail';
				$contact[0]['lang'] = Core\Registry::get('Lang')->t('system', 'email_address');
				$contact[0]['value'] = isset($_POST['submit']) ? $_POST['mail'] : $user['mail'];
				$contact[0]['maxlength'] = '120';
				$contact[1]['name'] = 'website';
				$contact[1]['lang'] = Core\Registry::get('Lang')->t('system', 'website');
				$contact[1]['value'] = isset($_POST['submit']) ? $_POST['website'] : $user['website'];
				$contact[1]['maxlength'] = '120';
				$contact[2]['name'] = 'icq';
				$contact[2]['lang'] = Core\Registry::get('Lang')->t('users', 'icq');
				$contact[2]['value'] = isset($_POST['submit']) ? $_POST['icq'] : $user['icq'];
				$contact[2]['maxlength'] = '9';
				$contact[3]['name'] = 'skype';
				$contact[3]['lang'] = Core\Registry::get('Lang')->t('users', 'skype');
				$contact[3]['value'] = isset($_POST['submit']) ? $_POST['skype'] : $user['skype'];
				$contact[3]['maxlength'] = '28';
				Core\Registry::get('View')->assign('contact', $contact);

				$countries = ACP3_LANG::worldCountries();
				$countries_select = array();
				foreach ($countries as $key => $value) {
					$countries_select[] = array(
						'value' => $key,
						'lang' => $value,
						'selected' => Core\Functions::selectEntry('countries', $key, $user['country']),
					);
				}
				Core\Registry::get('View')->assign('countries', $countries_select);

				$lang_mail_display = array(Core\Registry::get('Lang')->t('system', 'yes'), Core\Registry::get('Lang')->t('system', 'no'));
				Core\Registry::get('View')->assign('mail_display', Core\Functions::selectGenerator('mail_display', array(1, 0), $lang_mail_display, $user['mail_display'], 'checked'));

				$lang_address_display = array(Core\Registry::get('Lang')->t('system', 'yes'), Core\Registry::get('Lang')->t('system', 'no'));
				Core\Registry::get('View')->assign('address_display', Core\Functions::selectGenerator('address_display', array(1, 0), $lang_address_display, $user['address_display'], 'checked'));

				$lang_country_display = array(Core\Registry::get('Lang')->t('system', 'yes'), Core\Registry::get('Lang')->t('system', 'no'));
				Core\Registry::get('View')->assign('country_display', Core\Functions::selectGenerator('country_display', array(1, 0), $lang_country_display, $user['country_display'], 'checked'));

				$lang_birthday_display = array(
					Core\Registry::get('Lang')->t('users', 'birthday_hide'),
					Core\Registry::get('Lang')->t('users', 'birthday_display_completely'),
					Core\Registry::get('Lang')->t('users', 'birthday_hide_year')
				);
				Core\Registry::get('View')->assign('birthday_display', Core\Functions::selectGenerator('birthday_display', array(0, 1, 2), $lang_birthday_display, $user['birthday_display'], 'checked'));

				Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : $user);

				Core\Registry::get('Session')->generateFormToken();
			}
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionSettings()
	{
		if (isset($_POST['submit']) === true) {
			if (!empty($_POST['mail']) && Core\Validate::email($_POST['mail']) === false)
				$errors['mail'] = Core\Registry::get('Lang')->t('system', 'wrong_email_format');
			if (!isset($_POST['language_override']) || $_POST['language_override'] != 1 && $_POST['language_override'] != 0)
				$errors[] = Core\Registry::get('Lang')->t('users', 'select_languages_override');
			if (!isset($_POST['entries_override']) || $_POST['entries_override'] != 1 && $_POST['entries_override'] != 0)
				$errors[] = Core\Registry::get('Lang')->t('users', 'select_entries_override');
			if (!isset($_POST['enable_registration']) || $_POST['enable_registration'] != 1 && $_POST['enable_registration'] != 0)
				$errors[] = Core\Registry::get('Lang')->t('users', 'select_enable_registration');

			if (isset($errors) === true) {
				Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
			} else {
				$data = array(
					'enable_registration' => $_POST['enable_registration'],
					'entries_override' => $_POST['entries_override'],
					'language_override' => $_POST['language_override'],
					'mail' => $_POST['mail']
				);
				$bool = Core\Config::setSettings('users', $data);

				Core\Registry::get('Session')->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/users');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			$settings = Core\Config::getSettings('users');

			$lang_languages = array(Core\Registry::get('Lang')->t('system', 'yes'), Core\Registry::get('Lang')->t('system', 'no'));
			Core\Registry::get('View')->assign('languages', Core\Functions::selectGenerator('language_override', array(1, 0), $lang_languages, $settings['language_override'], 'checked'));

			$lang_entries = array(Core\Registry::get('Lang')->t('system', 'yes'), Core\Registry::get('Lang')->t('system', 'no'));
			Core\Registry::get('View')->assign('entries', Core\Functions::selectGenerator('entries_override', array(1, 0), $lang_entries, $settings['entries_override'], 'checked'));

			$lang_registration = array(Core\Registry::get('Lang')->t('system', 'yes'), Core\Registry::get('Lang')->t('system', 'no'));
			Core\Registry::get('View')->assign('registration', Core\Functions::selectGenerator('enable_registration', array(1, 0), $lang_registration, $settings['enable_registration'], 'checked'));

			Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : array('mail' => $settings['mail']));

			Core\Registry::get('Session')->generateFormToken();
		}
	}

	public function actionList()
	{
		Core\Functions::getRedirectMessage();

		$users = Core\Registry::get('Db')->fetchAll('SELECT id, nickname, mail FROM ' . DB_PRE . 'users ORDER BY nickname ASC');
		$c_users = count($users);

		if ($c_users > 0) {
			$can_delete = Core\Modules::check('users', 'acp_delete');
			$config = array(
				'element' => '#acp-table',
				'sort_col' => $can_delete === true ? 1 : 0,
				'sort_dir' => 'asc',
				'hide_col_sort' => $can_delete === true ? 0 : ''
			);
			Core\Registry::get('View')->appendContent(Core\Functions::datatable($config));

			for ($i = 0; $i < $c_users; ++$i) {
				$users[$i]['roles'] = implode(', ', Core\ACL::getUserRoles($users[$i]['id'], 2));
			}
			Core\Registry::get('View')->assign('users', $users);
			Core\Registry::get('View')->assign('can_delete', $can_delete);
		}
	}

}