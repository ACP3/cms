<?php

namespace ACP3\Modules\Users;

use ACP3\Core;

/**
 * Description of UsersAdmin
 *
 * @author Tino
 */
class UsersAdmin extends Core\ModuleController {

	public function __construct($injector)
	{
		parent::__construct($injector);
	}

	public function actionCreate()
	{
		if (isset($_POST['submit']) === true) {
			require_once MODULES_DIR . 'users/functions.php';

			if (empty($_POST['nickname']))
				$errors['nickname'] = $this->injector['Lang']->t('system', 'name_to_short');
			if (Core\Validate::gender($_POST['gender']) === false)
				$errors['gender'] = $this->injector['Lang']->t('users', 'select_gender');
			if (!empty($_POST['birthday']) && Core\Validate::birthday($_POST['birthday']) === false)
				$errors[] = $this->injector['Lang']->t('users', 'invalid_birthday');
			if (userNameExists($_POST['nickname']) === true)
				$errors['nickname'] = $this->injector['Lang']->t('users', 'user_name_already_exists');
			if (Core\Validate::email($_POST['mail']) === false)
				$errors['mail'] = $this->injector['Lang']->t('system', 'wrong_email_format');
			if (userEmailExists($_POST['mail']) === true)
				$errors['mail'] = $this->injector['Lang']->t('users', 'user_email_already_exists');
			if (empty($_POST['roles']) || is_array($_POST['roles']) === false || Core\Validate::aclRolesExist($_POST['roles']) === false)
				$errors['roles'] = $this->injector['Lang']->t('users', 'select_access_level');
			if (!isset($_POST['super_user']) || ($_POST['super_user'] != 1 && $_POST['super_user'] != 0))
				$errors['super-user'] = $this->injector['Lang']->t('users', 'select_super_user');
			if ($this->injector['Lang']->languagePackExists($_POST['language']) === false)
				$errors['language'] = $this->injector['Lang']->t('users', 'select_language');
			if (Core\Validate::isNumber($_POST['entries']) === false)
				$errors['entries'] = $this->injector['Lang']->t('system', 'select_records_per_page');
			if (empty($_POST['date_format_long']) || empty($_POST['date_format_short']))
				$errors[] = $this->injector['Lang']->t('system', 'type_in_date_format');
			if (Core\Validate::timeZone($_POST['date_time_zone']) === false)
				$errors['time-zone'] = $this->injector['Lang']->t('system', 'select_time_zone');
			if (!empty($_POST['icq']) && Core\Validate::icq($_POST['icq']) === false)
				$errors['icq'] = $this->injector['Lang']->t('users', 'invalid_icq_number');
			if (in_array($_POST['mail_display'], array(0, 1)) === false)
				$errors[] = $this->injector['Lang']->t('users', 'select_mail_display');
			if (in_array($_POST['address_display'], array(0, 1)) === false)
				$errors[] = $this->injector['Lang']->t('users', 'select_address_display');
			if (in_array($_POST['country_display'], array(0, 1)) === false)
				$errors[] = $this->injector['Lang']->t('users', 'select_country_display');
			if (in_array($_POST['birthday_display'], array(0, 1, 2)) === false)
				$errors[] = $this->injector['Lang']->t('users', 'select_birthday_display');
			if (empty($_POST['pwd']) || empty($_POST['pwd_repeat']) || $_POST['pwd'] != $_POST['pwd_repeat'])
				$errors[] = $this->injector['Lang']->t('users', 'type_in_pwd');

			if (isset($errors) === true) {
				$this->injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'form_already_submitted')));
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

				$this->injector['Db']->beginTransaction();
				try {
					$bool = $this->injector['Db']->insert(DB_PRE . 'users', $insert_values);
					$user_id = $this->injector['Db']->lastInsertId();
					foreach ($_POST['roles'] as $row) {
						$this->injector['Db']->insert(DB_PRE . 'acl_user_roles', array('user_id' => $user_id, 'role_id' => $row));
					}
					$this->injector['Db']->commit();
				} catch (\Exception $e) {
					$this->injector['Db']->rollback();
					$bool = false;
				}

				$this->injector['Session']->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/users');
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
			$this->injector['View']->assign('roles', $roles);

			// Super User
			$lang_super_user = array($this->injector['Lang']->t('system', 'yes'), $this->injector['Lang']->t('system', 'no'));
			$this->injector['View']->assign('super_user', Core\Functions::selectGenerator('super_user', array(1, 0), $lang_super_user, 0, 'checked'));

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
			$this->injector['View']->assign('languages', $languages);

			// Einträge pro Seite
			$this->injector['View']->assign('entries', Core\Functions::recordsPerPage(CONFIG_ENTRIES));

			// Zeitzonen
			$this->injector['View']->assign('time_zones', $this->injector['Date']->getTimeZones(CONFIG_DATE_TIME_ZONE));

			// Geschlecht
			$lang_gender = array(
				$this->injector['Lang']->t('users', 'gender_not_specified'),
				$this->injector['Lang']->t('users', 'gender_female'),
				$this->injector['Lang']->t('users', 'gender_male')
			);
			$this->injector['View']->assign('gender', Core\Functions::selectGenerator('gender', array(1, 2, 3), $lang_gender, ''));

			// Geburtstag
			$this->injector['View']->assign('birthday_datepicker', $this->injector['Date']->datepicker('birthday', '', 'Y-m-d', array('constrainInput' => 'true', 'changeMonth' => 'true', 'changeYear' => 'true', 'yearRange' => '\'-50:+0\''), 0, 1, false, true));

			// Kontaktangaben
			$contact = array();
			$contact[0]['name'] = 'mail';
			$contact[0]['lang'] = $this->injector['Lang']->t('system', 'email_address');
			$contact[0]['value'] = isset($_POST['submit']) ? $_POST['mail'] : '';
			$contact[0]['maxlength'] = '120';
			$contact[1]['name'] = 'website';
			$contact[1]['lang'] = $this->injector['Lang']->t('system', 'website');
			$contact[1]['value'] = isset($_POST['submit']) ? $_POST['website'] : '';
			$contact[1]['maxlength'] = '120';
			$contact[2]['name'] = 'icq';
			$contact[2]['lang'] = $this->injector['Lang']->t('users', 'icq');
			$contact[2]['value'] = isset($_POST['submit']) ? $_POST['icq'] : '';
			$contact[2]['maxlength'] = '9';
			$contact[3]['name'] = 'skype';
			$contact[3]['lang'] = $this->injector['Lang']->t('users', 'skype');
			$contact[3]['value'] = isset($_POST['submit']) ? $_POST['skype'] : '';
			$contact[3]['maxlength'] = '28';
			$this->injector['View']->assign('contact', $contact);

			$countries = Core\Lang::worldCountries();
			$countries_select = array();
			foreach ($countries as $key => $value) {
				$countries_select[] = array(
					'value' => $key,
					'lang' => $value,
					'selected' => Core\Functions::selectEntry('countries', $key),
				);
			}
			$this->injector['View']->assign('countries', $countries_select);

			$lang_mail_display = array($this->injector['Lang']->t('system', 'yes'), $this->injector['Lang']->t('system', 'no'));
			$this->injector['View']->assign('mail_display', Core\Functions::selectGenerator('mail_display', array(1, 0), $lang_mail_display, 0, 'checked'));

			$lang_address_display = array($this->injector['Lang']->t('system', 'yes'), $this->injector['Lang']->t('system', 'no'));
			$this->injector['View']->assign('address_display', Core\Functions::selectGenerator('address_display', array(1, 0), $lang_address_display, 0, 'checked'));

			$lang_country_display = array($this->injector['Lang']->t('system', 'yes'), $this->injector['Lang']->t('system', 'no'));
			$this->injector['View']->assign('country_display', Core\Functions::selectGenerator('country_display', array(1, 0), $lang_country_display, 0, 'checked'));

			$lang_birthday_display = array(
				$this->injector['Lang']->t('users', 'birthday_hide'),
				$this->injector['Lang']->t('users', 'birthday_display_completely'),
				$this->injector['Lang']->t('users', 'birthday_hide_year')
			);
			$this->injector['View']->assign('birthday_display', Core\Functions::selectGenerator('birthday_display', array(0, 1, 2), $lang_birthday_display, 0, 'checked'));

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

			$this->injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $defaults);

			$this->injector['Session']->generateFormToken();
		}
	}

	public function actionDelete()
	{
		if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
			$entries = $_POST['entries'];
		elseif (Core\Validate::deleteEntries($this->injector['URI']->entries) === true)
			$entries = $this->injector['URI']->entries;

		if (!isset($entries)) {
			$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'no_entries_selected')));
		} elseif (is_array($entries) === true) {
			$marked_entries = implode('|', $entries);
			$this->injector['View']->setContent(confirmBox($this->injector['Lang']->t('system', 'confirm_delete'), $this->injector['URI']->route('acp/users/delete/entries_' . $marked_entries . '/action_confirmed/'), $this->injector['URI']->route('acp/users')));
		} elseif ($this->injector['URI']->action === 'confirmed') {
			$marked_entries = explode('|', $entries);
			$bool = false;
			$admin_user = false;
			$self_delete = false;
			foreach ($marked_entries as $entry) {
				if ($entry == 1) {
					$admin_user = true;
				} else {
					// Falls sich der User selbst gelöscht hat, diesen auch gleich abmelden
					if ($entry == $this->injector['Auth']->getUserId()) {
						$this->injector['Auth']->logout();
						$self_delete = true;
					}
					$bool = $this->injector['Db']->delete(DB_PRE . 'users', array('id' => $entry));
				}
			}
			if ($admin_user === true) {
				$bool = false;
				$text = $this->injector['Lang']->t('users', 'admin_user_undeletable');
			} else {
				$text = $this->injector['Lang']->t('system', $bool !== false ? 'delete_success' : 'delete_error');
			}
			Core\Functions::setRedirectMessage($bool, $text, $self_delete === true ? ROOT_DIR : 'acp/users');
		} else {
			$this->injector['URI']->redirect('errors/404');
		}
	}

	public function actionEdit()
	{
		if (Core\Validate::isNumber($this->injector['URI']->id) === true &&
				$this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'users WHERE id = ?', array($this->injector['URI']->id)) == 1) {
			$user = $this->injector['Auth']->getUserInfo($this->injector['URI']->id);

			if (isset($_POST['submit']) === true) {
				require_once MODULES_DIR . 'users/functions.php';

				if (empty($_POST['nickname']))
					$errors['nickname'] = $this->injector['Lang']->t('system', 'name_to_short');
				if (Core\Validate::gender($_POST['gender']) === false)
					$errors['gender'] = $this->injector['Lang']->t('users', 'select_gender');
				if (!empty($_POST['birthday']) && Core\Validate::birthday($_POST['birthday']) === false)
					$errors[] = $this->injector['Lang']->t('users', 'invalid_birthday');
				if (userNameExists($_POST['nickname'], $this->injector['URI']->id))
					$errors['nickname'] = $this->injector['Lang']->t('users', 'user_name_already_exists');
				if (Core\Validate::email($_POST['mail']) === false)
					$errors['mail'] = $this->injector['Lang']->t('system', 'wrong_email_format');
				if (userEmailExists($_POST['mail'], $this->injector['URI']->id))
					$errors['mail'] = $this->injector['Lang']->t('users', 'user_email_already_exists');
				if (empty($_POST['roles']) || is_array($_POST['roles']) === false || Core\Validate::aclRolesExist($_POST['roles']) === false)
					$errors['roles'] = $this->injector['Lang']->t('users', 'select_access_level');
				if (!isset($_POST['super_user']) || ($_POST['super_user'] != 1 && $_POST['super_user'] != 0))
					$errors['super-user'] = $this->injector['Lang']->t('users', 'select_super_user');
				if ($this->injector['Lang']->languagePackExists($_POST['language']) === false)
					$errors['language'] = $this->injector['Lang']->t('users', 'select_language');
				if (Core\Validate::isNumber($_POST['entries']) === false)
					$errors['entries'] = $this->injector['Lang']->t('system', 'select_records_per_page');
				if (empty($_POST['date_format_long']) || empty($_POST['date_format_short']))
					$errors[] = $this->injector['Lang']->t('system', 'type_in_date_format');
				if (Core\Validate::timeZone($_POST['date_time_zone']) === false)
					$errors['time-zone'] = $this->injector['Lang']->t('system', 'select_time_zone');
				if (!empty($_POST['icq']) && Core\Validate::icq($_POST['icq']) === false)
					$errors['icq'] = $this->injector['Lang']->t('users', 'invalid_icq_number');
				if (in_array($_POST['mail_display'], array(0, 1)) === false)
					$errors[] = $this->injector['Lang']->t('users', 'select_mail_display');
				if (in_array($_POST['address_display'], array(0, 1)) === false)
					$errors[] = $this->injector['Lang']->t('users', 'select_address_display');
				if (in_array($_POST['country_display'], array(0, 1)) === false)
					$errors[] = $this->injector['Lang']->t('users', 'select_country_display');
				if (in_array($_POST['birthday_display'], array(0, 1, 2)) === false)
					$errors[] = $this->injector['Lang']->t('users', 'select_birthday_display');
				if (!empty($_POST['new_pwd']) && !empty($_POST['new_pwd_repeat']) && $_POST['new_pwd'] != $_POST['new_pwd_repeat'])
					$errors[] = $this->injector['Lang']->t('users', 'type_in_pwd');

				if (isset($errors) === true) {
					$this->injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'form_already_submitted')));
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
					$this->injector['Db']->beginTransaction();
					try {
						$this->injector['Db']->delete(DB_PRE . 'acl_user_roles', array('user_id' => $this->injector['URI']->id));
						foreach ($_POST['roles'] as $row) {
							$this->injector['Db']->insert(DB_PRE . 'acl_user_roles', array('user_id' => $this->injector['URI']->id, 'role_id' => $row));
						}
						$this->injector['Db']->commit();
					} catch (\Exception $e) {
						$this->injector['Db']->rollback();
					}

					// Neues Passwort
					if (!empty($_POST['new_pwd']) && !empty($_POST['new_pwd_repeat'])) {
						$salt = salt(12);
						$new_pwd = generateSaltedPassword($salt, $_POST['new_pwd']);
						$update_values['pwd'] = $new_pwd . ':' . $salt;
					}

					$bool = $this->injector['Db']->update(DB_PRE . 'users', $update_values, array('id' => $this->injector['URI']->id));

					// Falls sich der User selbst bearbeitet hat, Cookie aktualisieren
					if ($this->injector['URI']->id == $this->injector['Auth']->getUserId()) {
						$cookie_arr = explode('|', base64_decode($_COOKIE['ACP3_AUTH']));
						$this->injector['Auth']->setCookie($_POST['nickname'], isset($new_pwd) ? $new_pwd : $cookie_arr[1], 3600);
					}

					$this->injector['Session']->unsetFormToken();

					Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/users');
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				// Zugriffslevel holen
				$roles = Core\ACL::getAllRoles();
				$c_roles = count($roles);
				$user_roles = Core\ACL::getUserRoles($this->injector['URI']->id);
				for ($i = 0; $i < $c_roles; ++$i) {
					$roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
					$roles[$i]['selected'] = Core\Functions::selectEntry('roles', $roles[$i]['id'], in_array($roles[$i]['id'], $user_roles) ? $roles[$i]['id'] : '');
				}
				$this->injector['View']->assign('roles', $roles);

				// Super User
				$lang_super_user = array($this->injector['Lang']->t('system', 'yes'), $this->injector['Lang']->t('system', 'no'));
				$this->injector['View']->assign('super_user', Core\Functions::selectGenerator('super_user', array(1, 0), $lang_super_user, $user['super_user'], 'checked'));

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
				$this->injector['View']->assign('languages', $languages);

				// Einträge pro Seite
				$this->injector['View']->assign('entries', Core\Functions::recordsPerPage((int) $user['entries']));

				// Zeitzonen
				$this->injector['View']->assign('time_zones', $this->injector['Date']->getTimeZones($user['time_zone']));

				// Geschlecht
				$lang_gender = array(
					$this->injector['Lang']->t('users', 'gender_not_specified'),
					$this->injector['Lang']->t('users', 'gender_female'),
					$this->injector['Lang']->t('users', 'gender_male')
				);
				$this->injector['View']->assign('gender', Core\Functions::selectGenerator('gender', array(1, 2, 3), $lang_gender, $user['gender']));

				// Geburtstag
				$this->injector['View']->assign('birthday_datepicker', $this->injector['Date']->datepicker('birthday', $user['birthday'], 'Y-m-d', array('constrainInput' => 'true', 'changeMonth' => 'true', 'changeYear' => 'true', 'yearRange' => '\'-50:+0\''), 0, 1, false, true));

				// Kontaktangaben
				$contact = array();
				$contact[0]['name'] = 'mail';
				$contact[0]['lang'] = $this->injector['Lang']->t('system', 'email_address');
				$contact[0]['value'] = isset($_POST['submit']) ? $_POST['mail'] : $user['mail'];
				$contact[0]['maxlength'] = '120';
				$contact[1]['name'] = 'website';
				$contact[1]['lang'] = $this->injector['Lang']->t('system', 'website');
				$contact[1]['value'] = isset($_POST['submit']) ? $_POST['website'] : $user['website'];
				$contact[1]['maxlength'] = '120';
				$contact[2]['name'] = 'icq';
				$contact[2]['lang'] = $this->injector['Lang']->t('users', 'icq');
				$contact[2]['value'] = isset($_POST['submit']) ? $_POST['icq'] : $user['icq'];
				$contact[2]['maxlength'] = '9';
				$contact[3]['name'] = 'skype';
				$contact[3]['lang'] = $this->injector['Lang']->t('users', 'skype');
				$contact[3]['value'] = isset($_POST['submit']) ? $_POST['skype'] : $user['skype'];
				$contact[3]['maxlength'] = '28';
				$this->injector['View']->assign('contact', $contact);

				$countries = ACP3_LANG::worldCountries();
				$countries_select = array();
				foreach ($countries as $key => $value) {
					$countries_select[] = array(
						'value' => $key,
						'lang' => $value,
						'selected' => Core\Functions::selectEntry('countries', $key, $user['country']),
					);
				}
				$this->injector['View']->assign('countries', $countries_select);

				$lang_mail_display = array($this->injector['Lang']->t('system', 'yes'), $this->injector['Lang']->t('system', 'no'));
				$this->injector['View']->assign('mail_display', Core\Functions::selectGenerator('mail_display', array(1, 0), $lang_mail_display, $user['mail_display'], 'checked'));

				$lang_address_display = array($this->injector['Lang']->t('system', 'yes'), $this->injector['Lang']->t('system', 'no'));
				$this->injector['View']->assign('address_display', Core\Functions::selectGenerator('address_display', array(1, 0), $lang_address_display, $user['address_display'], 'checked'));

				$lang_country_display = array($this->injector['Lang']->t('system', 'yes'), $this->injector['Lang']->t('system', 'no'));
				$this->injector['View']->assign('country_display', Core\Functions::selectGenerator('country_display', array(1, 0), $lang_country_display, $user['country_display'], 'checked'));

				$lang_birthday_display = array(
					$this->injector['Lang']->t('users', 'birthday_hide'),
					$this->injector['Lang']->t('users', 'birthday_display_completely'),
					$this->injector['Lang']->t('users', 'birthday_hide_year')
				);
				$this->injector['View']->assign('birthday_display', Core\Functions::selectGenerator('birthday_display', array(0, 1, 2), $lang_birthday_display, $user['birthday_display'], 'checked'));

				$this->injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $user);

				$this->injector['Session']->generateFormToken();
			}
		} else {
			$this->injector['URI']->redirect('errors/404');
		}
	}

	public function actionSettings()
	{
		if (isset($_POST['submit']) === true) {
			if (!empty($_POST['mail']) && Core\Validate::email($_POST['mail']) === false)
				$errors['mail'] = $this->injector['Lang']->t('system', 'wrong_email_format');
			if (!isset($_POST['language_override']) || $_POST['language_override'] != 1 && $_POST['language_override'] != 0)
				$errors[] = $this->injector['Lang']->t('users', 'select_languages_override');
			if (!isset($_POST['entries_override']) || $_POST['entries_override'] != 1 && $_POST['entries_override'] != 0)
				$errors[] = $this->injector['Lang']->t('users', 'select_entries_override');
			if (!isset($_POST['enable_registration']) || $_POST['enable_registration'] != 1 && $_POST['enable_registration'] != 0)
				$errors[] = $this->injector['Lang']->t('users', 'select_enable_registration');

			if (isset($errors) === true) {
				$this->injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'form_already_submitted')));
			} else {
				$data = array(
					'enable_registration' => $_POST['enable_registration'],
					'entries_override' => $_POST['entries_override'],
					'language_override' => $_POST['language_override'],
					'mail' => $_POST['mail']
				);
				$bool = Core\Config::setSettings('users', $data);

				$this->injector['Session']->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/users');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			$settings = Core\Config::getSettings('users');

			$lang_languages = array($this->injector['Lang']->t('system', 'yes'), $this->injector['Lang']->t('system', 'no'));
			$this->injector['View']->assign('languages', Core\Functions::selectGenerator('language_override', array(1, 0), $lang_languages, $settings['language_override'], 'checked'));

			$lang_entries = array($this->injector['Lang']->t('system', 'yes'), $this->injector['Lang']->t('system', 'no'));
			$this->injector['View']->assign('entries', Core\Functions::selectGenerator('entries_override', array(1, 0), $lang_entries, $settings['entries_override'], 'checked'));

			$lang_registration = array($this->injector['Lang']->t('system', 'yes'), $this->injector['Lang']->t('system', 'no'));
			$this->injector['View']->assign('registration', Core\Functions::selectGenerator('enable_registration', array(1, 0), $lang_registration, $settings['enable_registration'], 'checked'));

			$this->injector['View']->assign('form', isset($_POST['submit']) ? $_POST : array('mail' => $settings['mail']));

			$this->injector['Session']->generateFormToken();
		}
	}

	public function actionList()
	{
		Core\Functions::getRedirectMessage();

		$users = $this->injector['Db']->fetchAll('SELECT id, nickname, mail FROM ' . DB_PRE . 'users ORDER BY nickname ASC');
		$c_users = count($users);

		if ($c_users > 0) {
			$can_delete = Core\Modules::check('users', 'acp_delete');
			$config = array(
				'element' => '#acp-table',
				'sort_col' => $can_delete === true ? 1 : 0,
				'sort_dir' => 'asc',
				'hide_col_sort' => $can_delete === true ? 0 : ''
			);
			$this->injector['View']->appendContent(Core\Functions::datatable($config));

			for ($i = 0; $i < $c_users; ++$i) {
				$users[$i]['roles'] = implode(', ', Core\ACL::getUserRoles($users[$i]['id'], 2));
			}
			$this->injector['View']->assign('users', $users);
			$this->injector['View']->assign('can_delete', $can_delete);
		}
	}

}