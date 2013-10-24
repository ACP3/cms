<?php

namespace ACP3\Modules\Users;

use ACP3\Core;

/**
 * Description of UsersAdmin
 *
 * @author Tino Goratsch
 */
class Admin extends Core\ModuleController {

	public function __construct() {
		parent::__construct();
	}

	public function actionCreate()
	{
		if (isset($_POST['submit']) === true) {
			if (empty($_POST['nickname']))
				$errors['nickname'] = $this->lang->t('system', 'name_to_short');
			if (Core\Validate::gender($_POST['gender']) === false)
				$errors['gender'] = $this->lang->t('users', 'select_gender');
			if (!empty($_POST['birthday']) && Core\Validate::birthday($_POST['birthday']) === false)
				$errors[] = $this->lang->t('users', 'invalid_birthday');
			if (Helpers::userNameExists($_POST['nickname']) === true)
				$errors['nickname'] = $this->lang->t('users', 'user_name_already_exists');
			if (Core\Validate::email($_POST['mail']) === false)
				$errors['mail'] = $this->lang->t('system', 'wrong_email_format');
			if (Helpers::userEmailExists($_POST['mail']) === true)
				$errors['mail'] = $this->lang->t('users', 'user_email_already_exists');
			if (empty($_POST['roles']) || is_array($_POST['roles']) === false || Core\Validate::aclRolesExist($_POST['roles']) === false)
				$errors['roles'] = $this->lang->t('users', 'select_access_level');
			if (!isset($_POST['super_user']) || ($_POST['super_user'] != 1 && $_POST['super_user'] != 0))
				$errors['super-user'] = $this->lang->t('users', 'select_super_user');
			if ($this->lang->languagePackExists($_POST['language']) === false)
				$errors['language'] = $this->lang->t('users', 'select_language');
			if (Core\Validate::isNumber($_POST['entries']) === false)
				$errors['entries'] = $this->lang->t('system', 'select_records_per_page');
			if (empty($_POST['date_format_long']) || empty($_POST['date_format_short']))
				$errors[] = $this->lang->t('system', 'type_in_date_format');
			if (Core\Validate::timeZone($_POST['date_time_zone']) === false)
				$errors['time-zone'] = $this->lang->t('system', 'select_time_zone');
			if (!empty($_POST['icq']) && Core\Validate::icq($_POST['icq']) === false)
				$errors['icq'] = $this->lang->t('users', 'invalid_icq_number');
			if (in_array($_POST['mail_display'], array(0, 1)) === false)
				$errors[] = $this->lang->t('users', 'select_mail_display');
			if (in_array($_POST['address_display'], array(0, 1)) === false)
				$errors[] = $this->lang->t('users', 'select_address_display');
			if (in_array($_POST['country_display'], array(0, 1)) === false)
				$errors[] = $this->lang->t('users', 'select_country_display');
			if (in_array($_POST['birthday_display'], array(0, 1, 2)) === false)
				$errors[] = $this->lang->t('users', 'select_birthday_display');
			if (empty($_POST['pwd']) || empty($_POST['pwd_repeat']) || $_POST['pwd'] != $_POST['pwd_repeat'])
				$errors[] = $this->lang->t('users', 'type_in_pwd');

			if (isset($errors) === true) {
				$this->view->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				$this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
			} else {
				$salt = Core\Functions::salt(12);

				$insert_values = array(
					'id' => '',
					'super_user' => (int) $_POST['super_user'],
					'nickname' => Core\Functions::strEncode($_POST['nickname']),
					'pwd' => Core\Functions::generateSaltedPassword($salt, $_POST['pwd']) . ':' . $salt,
					'realname' => Core\Functions::strEncode($_POST['realname']),
					'gender' => (int) $_POST['gender'],
					'birthday' => $_POST['birthday'],
					'birthday_display' => (int) $_POST['birthday_display'],
					'mail' => $_POST['mail'],
					'mail_display' => isset($_POST['mail_display']) ? 1 : 0,
					'website' => Core\Functions::strEncode($_POST['website']),
					'icq' => $_POST['icq'],
					'skype' => Core\Functions::strEncode($_POST['skype']),
					'street' => Core\Functions::strEncode($_POST['street']),
					'house_number' => Core\Functions::strEncode($_POST['house_number']),
					'zip' => Core\Functions::strEncode($_POST['zip']),
					'city' => Core\Functions::strEncode($_POST['city']),
					'address_display' => isset($_POST['address_display']) ? 1 : 0,
					'country' => Core\Functions::strEncode($_POST['country']),
					'country_display' => isset($_POST['country_display']) ? 1 : 0,
					'date_format_long' => Core\Functions::strEncode($_POST['date_format_long']),
					'date_format_short' => Core\Functions::strEncode($_POST['date_format_short']),
					'time_zone' => $_POST['date_time_zone'],
					'language' => $_POST['language'],
					'entries' => (int) $_POST['entries'],
					'draft' => '',
					'registration_date' => $this->date->getCurrentDateTime(),
				);

				$this->db->beginTransaction();
				try {
					$bool = $this->db->insert(DB_PRE . 'users', $insert_values);
					$user_id = $this->db->lastInsertId();
					foreach ($_POST['roles'] as $row) {
						$this->db->insert(DB_PRE . 'acl_user_roles', array('user_id' => $user_id, 'role_id' => $row));
					}
					$this->db->commit();
				} catch (\Exception $e) {
					$this->db->rollback();
					$bool = false;
				}

				$this->session->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'create_success' : 'create_error'), 'acp/users');
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
			$this->view->assign('roles', $roles);

			// Super User
			$lang_super_user = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
			$this->view->assign('super_user', Core\Functions::selectGenerator('super_user', array(1, 0), $lang_super_user, 0, 'checked'));

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
			$this->view->assign('languages', $languages);

			// Einträge pro Seite
			$this->view->assign('entries', Core\Functions::recordsPerPage(CONFIG_ENTRIES));

			// Zeitzonen
			$this->view->assign('time_zones', $this->date->getTimeZones(CONFIG_DATE_TIME_ZONE));

			// Geschlecht
			$lang_gender = array(
				$this->lang->t('users', 'gender_not_specified'),
				$this->lang->t('users', 'gender_female'),
				$this->lang->t('users', 'gender_male')
			);
			$this->view->assign('gender', Core\Functions::selectGenerator('gender', array(1, 2, 3), $lang_gender, ''));

			// Geburtstag
			$this->view->assign('birthday_datepicker', $this->date->datepicker('birthday', '', 'Y-m-d', array('constrainInput' => 'true', 'changeMonth' => 'true', 'changeYear' => 'true', 'yearRange' => '\'-50:+0\''), 0, 1, false, true));

			// Kontaktangaben
			$contact = array();
			$contact[0]['name'] = 'mail';
			$contact[0]['lang'] = $this->lang->t('system', 'email_address');
			$contact[0]['value'] = isset($_POST['submit']) ? $_POST['mail'] : '';
			$contact[0]['maxlength'] = '120';
			$contact[1]['name'] = 'website';
			$contact[1]['lang'] = $this->lang->t('system', 'website');
			$contact[1]['value'] = isset($_POST['submit']) ? $_POST['website'] : '';
			$contact[1]['maxlength'] = '120';
			$contact[2]['name'] = 'icq';
			$contact[2]['lang'] = $this->lang->t('users', 'icq');
			$contact[2]['value'] = isset($_POST['submit']) ? $_POST['icq'] : '';
			$contact[2]['maxlength'] = '9';
			$contact[3]['name'] = 'skype';
			$contact[3]['lang'] = $this->lang->t('users', 'skype');
			$contact[3]['value'] = isset($_POST['submit']) ? $_POST['skype'] : '';
			$contact[3]['maxlength'] = '28';
			$this->view->assign('contact', $contact);

			$countries = Core\Lang::worldCountries();
			$countries_select = array();
			foreach ($countries as $key => $value) {
				$countries_select[] = array(
					'value' => $key,
					'lang' => $value,
					'selected' => Core\Functions::selectEntry('countries', $key),
				);
			}
			$this->view->assign('countries', $countries_select);

			$lang_mail_display = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
			$this->view->assign('mail_display', Core\Functions::selectGenerator('mail_display', array(1, 0), $lang_mail_display, 0, 'checked'));

			$lang_address_display = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
			$this->view->assign('address_display', Core\Functions::selectGenerator('address_display', array(1, 0), $lang_address_display, 0, 'checked'));

			$lang_country_display = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
			$this->view->assign('country_display', Core\Functions::selectGenerator('country_display', array(1, 0), $lang_country_display, 0, 'checked'));

			$lang_birthday_display = array(
				$this->lang->t('users', 'birthday_hide'),
				$this->lang->t('users', 'birthday_display_completely'),
				$this->lang->t('users', 'birthday_hide_year')
			);
			$this->view->assign('birthday_display', Core\Functions::selectGenerator('birthday_display', array(0, 1, 2), $lang_birthday_display, 0, 'checked'));

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

			$this->view->assign('form', isset($_POST['submit']) ? $_POST : $defaults);

			$this->session->generateFormToken();
		}
	}

	public function actionDelete()
	{
		if (isset($_POST['entries']) && is_array($_POST['entries']) === true)
			$entries = $_POST['entries'];
		elseif (Core\Validate::deleteEntries($this->uri->entries) === true)
			$entries = $this->uri->entries;

		if (!isset($entries)) {
			$this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'no_entries_selected')));
		} elseif (is_array($entries) === true) {
			$marked_entries = implode('|', $entries);
			$this->view->setContent(Core\Functions::confirmBox($this->lang->t('system', 'confirm_delete'), $this->uri->route('acp/users/delete/entries_' . $marked_entries . '/action_confirmed/'), $this->uri->route('acp/users')));
		} elseif ($this->uri->action === 'confirmed') {
			$marked_entries = explode('|', $entries);
			$bool = false;
			$admin_user = false;
			$self_delete = false;
			foreach ($marked_entries as $entry) {
				if ($entry == 1) {
					$admin_user = true;
				} else {
					// Falls sich der User selbst gelöscht hat, diesen auch gleich abmelden
					if ($entry == $this->auth->getUserId()) {
						$this->auth->logout();
						$self_delete = true;
					}
					$bool = $this->db->delete(DB_PRE . 'users', array('id' => $entry));
				}
			}
			if ($admin_user === true) {
				$bool = false;
				$text = $this->lang->t('users', 'admin_user_undeletable');
			} else {
				$text = $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error');
			}
			Core\Functions::setRedirectMessage($bool, $text, $self_delete === true ? ROOT_DIR : 'acp/users');
		} else {
			$this->uri->redirect('errors/404');
		}
	}

	public function actionEdit()
	{
		if (Core\Validate::isNumber($this->uri->id) === true &&
				$this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'users WHERE id = ?', array($this->uri->id)) == 1) {
			$user = $this->auth->getUserInfo($this->uri->id);

			if (isset($_POST['submit']) === true) {
				if (empty($_POST['nickname']))
					$errors['nickname'] = $this->lang->t('system', 'name_to_short');
				if (Core\Validate::gender($_POST['gender']) === false)
					$errors['gender'] = $this->lang->t('users', 'select_gender');
				if (!empty($_POST['birthday']) && Core\Validate::birthday($_POST['birthday']) === false)
					$errors[] = $this->lang->t('users', 'invalid_birthday');
				if (Helpers::userNameExists($_POST['nickname'], $this->uri->id))
					$errors['nickname'] = $this->lang->t('users', 'user_name_already_exists');
				if (Core\Validate::email($_POST['mail']) === false)
					$errors['mail'] = $this->lang->t('system', 'wrong_email_format');
				if (Helpers::userEmailExists($_POST['mail'], $this->uri->id))
					$errors['mail'] = $this->lang->t('users', 'user_email_already_exists');
				if (empty($_POST['roles']) || is_array($_POST['roles']) === false || Core\Validate::aclRolesExist($_POST['roles']) === false)
					$errors['roles'] = $this->lang->t('users', 'select_access_level');
				if (!isset($_POST['super_user']) || ($_POST['super_user'] != 1 && $_POST['super_user'] != 0))
					$errors['super-user'] = $this->lang->t('users', 'select_super_user');
				if ($this->lang->languagePackExists($_POST['language']) === false)
					$errors['language'] = $this->lang->t('users', 'select_language');
				if (Core\Validate::isNumber($_POST['entries']) === false)
					$errors['entries'] = $this->lang->t('system', 'select_records_per_page');
				if (empty($_POST['date_format_long']) || empty($_POST['date_format_short']))
					$errors[] = $this->lang->t('system', 'type_in_date_format');
				if (Core\Validate::timeZone($_POST['date_time_zone']) === false)
					$errors['time-zone'] = $this->lang->t('system', 'select_time_zone');
				if (!empty($_POST['icq']) && Core\Validate::icq($_POST['icq']) === false)
					$errors['icq'] = $this->lang->t('users', 'invalid_icq_number');
				if (in_array($_POST['mail_display'], array(0, 1)) === false)
					$errors[] = $this->lang->t('users', 'select_mail_display');
				if (in_array($_POST['address_display'], array(0, 1)) === false)
					$errors[] = $this->lang->t('users', 'select_address_display');
				if (in_array($_POST['country_display'], array(0, 1)) === false)
					$errors[] = $this->lang->t('users', 'select_country_display');
				if (in_array($_POST['birthday_display'], array(0, 1, 2)) === false)
					$errors[] = $this->lang->t('users', 'select_birthday_display');
				if (!empty($_POST['new_pwd']) && !empty($_POST['new_pwd_repeat']) && $_POST['new_pwd'] != $_POST['new_pwd_repeat'])
					$errors[] = $this->lang->t('users', 'type_in_pwd');

				if (isset($errors) === true) {
					$this->view->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					$this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
				} else {
					$update_values = array(
						'super_user' => (int) $_POST['super_user'],
						'nickname' => Core\Functions::strEncode($_POST['nickname']),
						'realname' => Core\Functions::strEncode($_POST['realname']),
						'gender' => (int) $_POST['gender'],
						'birthday' => $_POST['birthday'],
						'birthday_display' => (int) $_POST['birthday_display'],
						'mail' => $_POST['mail'],
						'mail_display' => (int) $_POST['mail_display'],
						'website' => Core\Functions::strEncode($_POST['website']),
						'icq' => $_POST['icq'],
						'skype' => Core\Functions::strEncode($_POST['skype']),
						'street' => Core\Functions::strEncode($_POST['street']),
						'house_number' => Core\Functions::strEncode($_POST['house_number']),
						'zip' => Core\Functions::strEncode($_POST['zip']),
						'city' => Core\Functions::strEncode($_POST['city']),
						'address_display' => (int) $_POST['address_display'],
						'country' => Core\Functions::strEncode($_POST['country']),
						'country_display' => (int) $_POST['country_display'],
						'date_format_long' => Core\Functions::strEncode($_POST['date_format_long']),
						'date_format_short' => Core\Functions::strEncode($_POST['date_format_short']),
						'time_zone' => $_POST['date_time_zone'],
						'language' => $_POST['language'],
						'entries' => (int) $_POST['entries'],
					);

					// Rollen aktualisieren
					$this->db->beginTransaction();
					try {
						$this->db->delete(DB_PRE . 'acl_user_roles', array('user_id' => $this->uri->id));
						foreach ($_POST['roles'] as $row) {
							$this->db->insert(DB_PRE . 'acl_user_roles', array('user_id' => $this->uri->id, 'role_id' => $row));
						}
						$this->db->commit();
					} catch (\Exception $e) {
						$this->db->rollback();
					}

					// Neues Passwort
					if (!empty($_POST['new_pwd']) && !empty($_POST['new_pwd_repeat'])) {
						$salt = Core\Functions::salt(12);
						$new_pwd = Core\Functions::generateSaltedPassword($salt, $_POST['new_pwd']);
						$update_values['pwd'] = $new_pwd . ':' . $salt;
					}

					$bool = $this->db->update(DB_PRE . 'users', $update_values, array('id' => $this->uri->id));

					// Falls sich der User selbst bearbeitet hat, Cookie aktualisieren
					if ($this->uri->id == $this->auth->getUserId()) {
						$cookie_arr = explode('|', base64_decode($_COOKIE['ACP3_AUTH']));
						$this->auth->setCookie($_POST['nickname'], isset($new_pwd) ? $new_pwd : $cookie_arr[1], 3600);
					}

					$this->session->unsetFormToken();

					Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/users');
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				// Zugriffslevel holen
				$roles = Core\ACL::getAllRoles();
				$c_roles = count($roles);
				$user_roles = Core\ACL::getUserRoles($this->uri->id);
				for ($i = 0; $i < $c_roles; ++$i) {
					$roles[$i]['name'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']) . $roles[$i]['name'];
					$roles[$i]['selected'] = Core\Functions::selectEntry('roles', $roles[$i]['id'], in_array($roles[$i]['id'], $user_roles) ? $roles[$i]['id'] : '');
				}
				$this->view->assign('roles', $roles);

				// Super User
				$lang_super_user = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
				$this->view->assign('super_user', Core\Functions::selectGenerator('super_user', array(1, 0), $lang_super_user, $user['super_user'], 'checked'));

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
				$this->view->assign('languages', $languages);

				// Einträge pro Seite
				$this->view->assign('entries', Core\Functions::recordsPerPage((int) $user['entries']));

				// Zeitzonen
				$this->view->assign('time_zones', Core\Date::getTimeZones($user['time_zone']));

				// Geschlecht
				$lang_gender = array(
					$this->lang->t('users', 'gender_not_specified'),
					$this->lang->t('users', 'gender_female'),
					$this->lang->t('users', 'gender_male')
				);
				$this->view->assign('gender', Core\Functions::selectGenerator('gender', array(1, 2, 3), $lang_gender, $user['gender']));

				// Geburtstag
				$this->view->assign('birthday_datepicker', $this->date->datepicker('birthday', $user['birthday'], 'Y-m-d', array('constrainInput' => 'true', 'changeMonth' => 'true', 'changeYear' => 'true', 'yearRange' => '\'-50:+0\''), 0, 1, false, true));

				// Kontaktangaben
				$contact = array();
				$contact[0]['name'] = 'mail';
				$contact[0]['lang'] = $this->lang->t('system', 'email_address');
				$contact[0]['value'] = isset($_POST['submit']) ? $_POST['mail'] : $user['mail'];
				$contact[0]['maxlength'] = '120';
				$contact[1]['name'] = 'website';
				$contact[1]['lang'] = $this->lang->t('system', 'website');
				$contact[1]['value'] = isset($_POST['submit']) ? $_POST['website'] : $user['website'];
				$contact[1]['maxlength'] = '120';
				$contact[2]['name'] = 'icq';
				$contact[2]['lang'] = $this->lang->t('users', 'icq');
				$contact[2]['value'] = isset($_POST['submit']) ? $_POST['icq'] : $user['icq'];
				$contact[2]['maxlength'] = '9';
				$contact[3]['name'] = 'skype';
				$contact[3]['lang'] = $this->lang->t('users', 'skype');
				$contact[3]['value'] = isset($_POST['submit']) ? $_POST['skype'] : $user['skype'];
				$contact[3]['maxlength'] = '28';
				$this->view->assign('contact', $contact);

				$countries = Core\Lang::worldCountries();
				$countries_select = array();
				foreach ($countries as $key => $value) {
					$countries_select[] = array(
						'value' => $key,
						'lang' => $value,
						'selected' => Core\Functions::selectEntry('countries', $key, $user['country']),
					);
				}
				$this->view->assign('countries', $countries_select);

				$lang_mail_display = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
				$this->view->assign('mail_display', Core\Functions::selectGenerator('mail_display', array(1, 0), $lang_mail_display, $user['mail_display'], 'checked'));

				$lang_address_display = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
				$this->view->assign('address_display', Core\Functions::selectGenerator('address_display', array(1, 0), $lang_address_display, $user['address_display'], 'checked'));

				$lang_country_display = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
				$this->view->assign('country_display', Core\Functions::selectGenerator('country_display', array(1, 0), $lang_country_display, $user['country_display'], 'checked'));

				$lang_birthday_display = array(
					$this->lang->t('users', 'birthday_hide'),
					$this->lang->t('users', 'birthday_display_completely'),
					$this->lang->t('users', 'birthday_hide_year')
				);
				$this->view->assign('birthday_display', Core\Functions::selectGenerator('birthday_display', array(0, 1, 2), $lang_birthday_display, $user['birthday_display'], 'checked'));

				$this->view->assign('form', isset($_POST['submit']) ? $_POST : $user);

				$this->session->generateFormToken();
			}
		} else {
			$this->uri->redirect('errors/404');
		}
	}

	public function actionSettings()
	{
		if (isset($_POST['submit']) === true) {
			if (!empty($_POST['mail']) && Core\Validate::email($_POST['mail']) === false)
				$errors['mail'] = $this->lang->t('system', 'wrong_email_format');
			if (!isset($_POST['language_override']) || $_POST['language_override'] != 1 && $_POST['language_override'] != 0)
				$errors[] = $this->lang->t('users', 'select_languages_override');
			if (!isset($_POST['entries_override']) || $_POST['entries_override'] != 1 && $_POST['entries_override'] != 0)
				$errors[] = $this->lang->t('users', 'select_entries_override');
			if (!isset($_POST['enable_registration']) || $_POST['enable_registration'] != 1 && $_POST['enable_registration'] != 0)
				$errors[] = $this->lang->t('users', 'select_enable_registration');

			if (isset($errors) === true) {
				$this->view->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (Core\Validate::formToken() === false) {
				$this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
			} else {
				$data = array(
					'enable_registration' => $_POST['enable_registration'],
					'entries_override' => $_POST['entries_override'],
					'language_override' => $_POST['language_override'],
					'mail' => $_POST['mail']
				);
				$bool = Core\Config::setSettings('users', $data);

				$this->session->unsetFormToken();

				Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/users');
			}
		}
		if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
			$settings = Core\Config::getSettings('users');

			$lang_languages = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
			$this->view->assign('languages', Core\Functions::selectGenerator('language_override', array(1, 0), $lang_languages, $settings['language_override'], 'checked'));

			$lang_entries = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
			$this->view->assign('entries', Core\Functions::selectGenerator('entries_override', array(1, 0), $lang_entries, $settings['entries_override'], 'checked'));

			$lang_registration = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
			$this->view->assign('registration', Core\Functions::selectGenerator('enable_registration', array(1, 0), $lang_registration, $settings['enable_registration'], 'checked'));

			$this->view->assign('form', isset($_POST['submit']) ? $_POST : array('mail' => $settings['mail']));

			$this->session->generateFormToken();
		}
	}

	public function actionList()
	{
		Core\Functions::getRedirectMessage();

		$users = $this->db->fetchAll('SELECT id, nickname, mail FROM ' . DB_PRE . 'users ORDER BY nickname ASC');
		$c_users = count($users);

		if ($c_users > 0) {
			$can_delete = Core\Modules::hasPermission('users', 'acp_delete');
			$config = array(
				'element' => '#acp-table',
				'sort_col' => $can_delete === true ? 1 : 0,
				'sort_dir' => 'asc',
				'hide_col_sort' => $can_delete === true ? 0 : ''
			);
			$this->view->appendContent(Core\Functions::datatable($config));

			for ($i = 0; $i < $c_users; ++$i) {
				$users[$i]['roles'] = implode(', ', Core\ACL::getUserRoles($users[$i]['id'], 2));
			}
			$this->view->assign('users', $users);
			$this->view->assign('can_delete', $can_delete);
		}
	}

}