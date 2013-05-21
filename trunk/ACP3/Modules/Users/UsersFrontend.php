<?php

namespace ACP3\Modules\Users;

use ACP3\Core;

/**
 * Description of UsersFrontend
 *
 * @author Tino
 */
class UsersFrontend extends Core\ModuleController {

	public function actionEditProfile()
	{
		if (Core\Registry::get('Auth')->isUser() === false || Core\Validate::isNumber(Core\Registry::get('Auth')->getUserId()) === false) {
			Core\Registry::get('URI')->redirect('errors/403');
		} else {
			Core\Registry::get('Breadcrumb')
					->append(Core\Registry::get('Lang')->t('users', 'users'), Core\Registry::get('URI')->route('users'))
					->append(Core\Registry::get('Lang')->t('users', 'home'), Core\Registry::get('URI')->route('users/home'))
					->append(Core\Registry::get('Lang')->t('users', 'edit_profile'));

			if (isset($_POST['submit']) === true) {
				if (empty($_POST['nickname']))
					$errors['nnickname'] = Core\Registry::get('Lang')->t('system', 'name_to_short');
				if (UsersFunctions::userNameExists($_POST['nickname'], Core\Registry::get('Auth')->getUserId()) === true)
					$errors['nickname'] = Core\Registry::get('Lang')->t('users', 'user_name_already_exists');
				if (Core\Validate::gender($_POST['gender']) === false)
					$errors['gender'] = Core\Registry::get('Lang')->t('users', 'select_gender');
				if (!empty($_POST['birthday']) && Core\Validate::birthday($_POST['birthday']) === false)
					$errors[] = Core\Registry::get('Lang')->t('users', 'invalid_birthday');
				if (Core\Validate::email($_POST['mail']) === false)
					$errors['mail'] = Core\Registry::get('Lang')->t('system', 'wrong_email_format');
				if (UsersFunctions::userEmailExists($_POST['mail'], Core\Registry::get('Auth')->getUserId()) === true)
					$errors['mail'] = Core\Registry::get('Lang')->t('users', 'user_email_already_exists');
				if (!empty($_POST['icq']) && Core\Validate::icq($_POST['icq']) === false)
					$errors['icq'] = Core\Registry::get('Lang')->t('users', 'invalid_icq_number');
				if (!empty($_POST['new_pwd']) && !empty($_POST['new_pwd_repeat']) && $_POST['new_pwd'] != $_POST['new_pwd_repeat'])
					$errors[] = Core\Registry::get('Lang')->t('users', 'type_in_pwd');

				if (isset($errors) === true) {
					Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
				} else {
					$update_values = array(
						'nickname' => Core\Functions::strEncode($_POST['nickname']),
						'realname' => Core\Functions::strEncode($_POST['realname']),
						'gender' => (int) $_POST['gender'],
						'birthday' => $_POST['birthday'],
						'mail' => $_POST['mail'],
						'website' => Core\Functions::strEncode($_POST['website']),
						'icq' => $_POST['icq'],
						'skype' => Core\Functions::strEncode($_POST['skype']),
						'street' => Core\Functions::strEncode($_POST['street']),
						'house_number' => Core\Functions::strEncode($_POST['house_number']),
						'zip' => Core\Functions::strEncode($_POST['zip']),
						'city' => Core\Functions::strEncode($_POST['city']),
						'country' => Core\Functions::strEncode($_POST['country']),
					);

					// Neues Passwort
					if (!empty($_POST['new_pwd']) && !empty($_POST['new_pwd_repeat'])) {
						$salt = salt(12);
						$new_pwd = Core\Functions::generateSaltedPassword($salt, $_POST['new_pwd']);
						$update_values['pwd'] = $new_pwd . ':' . $salt;
					}

					$bool = Core\Registry::get('Db')->update(DB_PRE . 'users', $update_values, array('id' => Core\Registry::get('Auth')->getUserId()));

					$cookie_arr = explode('|', base64_decode($_COOKIE['ACP3_AUTH']));
					Core\Registry::get('Auth')->setCookie($_POST['nickname'], isset($new_pwd) ? $new_pwd : $cookie_arr[1], 3600);

					Core\Registry::get('Session')->unsetFormToken();

					Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'users/home');
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				$user = Core\Registry::get('Auth')->getUserInfo();

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

				$countries = Core\Lang::worldCountries();
				$countries_select = array();
				foreach ($countries as $key => $value) {
					$countries_select[] = array(
						'value' => $key,
						'lang' => $value,
						'selected' => Core\Functions::selectEntry('countries', $key, $user['country']),
					);
				}
				Core\Registry::get('View')->assign('countries', $countries_select);

				Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : $user);

				Core\Registry::get('Session')->generateFormToken();
			}
		}
	}

	public function actionEditSettings()
	{
		if (Core\Registry::get('Auth')->isUser() === false || Core\Validate::isNumber(Core\Registry::get('Auth')->getUserId()) === false) {
			Core\Registry::get('URI')->redirect('errors/403');
		} else {
			$settings = Core\Config::getSettings('users');

			Core\Registry::get('Breadcrumb')
					->append(Core\Registry::get('Lang')->t('users', 'users'), Core\Registry::get('URI')->route('users'))
					->append(Core\Registry::get('Lang')->t('users', 'home'), Core\Registry::get('URI')->route('users/home'))
					->append(Core\Registry::get('Lang')->t('users', 'edit_settings'));

			if (isset($_POST['submit']) === true) {
				if ($settings['language_override'] == 1 && Core\Registry::get('Lang')->languagePackExists($_POST['language']) === false)
					$errors['language'] = Core\Registry::get('Lang')->t('users', 'select_language');
				if ($settings['entries_override'] == 1 && Core\Validate::isNumber($_POST['entries']) === false)
					$errors['entries'] = Core\Registry::get('Lang')->t('system', 'select_records_per_page');
				if (empty($_POST['date_format_long']) || empty($_POST['date_format_short']))
					$errors[] = Core\Registry::get('Lang')->t('system', 'type_in_date_format');
				if (Core\Validate::timeZone($_POST['date_time_zone']) === false)
					$errors['time-zone'] = Core\Registry::get('Lang')->t('system', 'select_time_zone');
				if (in_array($_POST['mail_display'], array(0, 1)) === false)
					$errors[] = Core\Registry::get('Lang')->t('users', 'select_mail_display');
				if (in_array($_POST['address_display'], array(0, 1)) === false)
					$errors[] = Core\Registry::get('Lang')->t('users', 'select_address_display');
				if (in_array($_POST['country_display'], array(0, 1)) === false)
					$errors[] = Core\Registry::get('Lang')->t('users', 'select_country_display');
				if (in_array($_POST['birthday_display'], array(0, 1, 2)) === false)
					$errors[] = Core\Registry::get('Lang')->t('users', 'select_birthday_display');

				if (isset($errors) === true) {
					Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
				} else {
					$update_values = array(
						'mail_display' => (int) $_POST['mail_display'],
						'birthday_display' => (int) $_POST['birthday_display'],
						'address_display' => (int) $_POST['address_display'],
						'country_display' => (int) $_POST['country_display'],
						'date_format_long' => Core\Functions::strEncode($_POST['date_format_long']),
						'date_format_short' => Core\Functions::strEncode($_POST['date_format_short']),
						'time_zone' => $_POST['date_time_zone'],
					);
					if ($settings['language_override'] == 1)
						$update_values['language'] = $_POST['language'];
					if ($settings['entries_override'] == 1)
						$update_values['entries'] = (int) $_POST['entries'];

					$bool = Core\Registry::get('Db')->update(DB_PRE . 'users', $update_values, array('id' => Core\Registry::get('Auth')->getUserId()));

					Core\Registry::get('Session')->unsetFormToken();

					Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'settings_success' : 'settings_error'), 'users/home');
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				$user = Core\Registry::get('Db')->fetchAssoc('SELECT mail_display, birthday_display, address_display, country_display, date_format_long, date_format_short, time_zone, language, entries FROM ' . DB_PRE . 'users WHERE id = ?', array(Core\Registry::get('Auth')->getUserId()));

				Core\Registry::get('View')->assign('language_override', $settings['language_override']);
				Core\Registry::get('View')->assign('entries_override', $settings['entries_override']);

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
		}
	}

	public function actionForgotPwd()
	{
		if (Core\Registry::get('Auth')->isUser() === true) {
			Core\Registry::get('URI')->redirect(0, ROOT_DIR);
		} else {
			Core\Registry::get('Breadcrumb')
					->append(Core\Registry::get('Lang')->t('users', 'users'), Core\Registry::get('URI')->route('users'))
					->append(Core\Registry::get('Lang')->t('users', 'forgot_pwd'));

			$captchaAccess = Core\Modules::hasPermission('captcha', 'image');

			if (isset($_POST['submit']) === true) {
				if (empty($_POST['nick_mail']))
					$errors['nick-mail'] = Core\Registry::get('Lang')->t('users', 'type_in_nickname_or_email');
				elseif (Core\Validate::email($_POST['nick_mail']) === false && UsersFunctions::userNameExists($_POST['nick_mail']) === false)
					$errors['nick-mail'] = Core\Registry::get('Lang')->t('users', 'user_not_exists');
				elseif (Core\Validate::email($_POST['nick_mail']) === true && UsersFunctions::userEmailExists($_POST['nick_mail']) === false)
					$errors['nick-mail'] = Core\Registry::get('Lang')->t('users', 'user_not_exists');
				if ($captchaAccess === true && Core\Validate::captcha($_POST['captcha']) === false)
					$errors['captcha'] = Core\Registry::get('Lang')->t('captcha', 'invalid_captcha_entered');

				if (isset($errors) === true) {
					Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
				} else {
					// Neues Passwort und neuen Zufallsschlüssel erstellen
					$new_password = salt(8);
					$host = htmlentities($_SERVER['HTTP_HOST']);

					// Je nachdem, wie das Feld ausgefüllt wurde, dieses auswählen
					if (Core\Validate::email($_POST['nick_mail']) === true && UsersFunctions::userEmailExists($_POST['nick_mail']) === true) {
						$query = 'SELECT id, nickname, realname, mail FROM ' . DB_PRE . 'users WHERE mail = ?';
					} else {
						$query = 'SELECT id, nickname, realname, mail FROM ' . DB_PRE . 'users WHERE nickname = ?';
					}
					$user = Core\Registry::get('Db')->fetchAssoc($query, array($_POST['nick_mail']));

					// E-Mail mit dem neuen Passwort versenden
					$subject = str_replace(array('{title}', '{host}'), array(CONFIG_SEO_TITLE, $host), Core\Registry::get('Lang')->t('users', 'forgot_pwd_mail_subject'));
					$search = array('{name}', '{mail}', '{password}', '{title}', '{host}');
					$replace = array($user['nickname'], $user['mail'], $new_password, CONFIG_SEO_TITLE, $host);
					$body = str_replace($search, $replace, Core\Registry::get('Lang')->t('users', 'forgot_pwd_mail_message'));

					$settings = Core\Config::getSettings('users');
					$mail_sent = Core\Functions::generateEmail(substr($user['realname'], 0, -2), $user['mail'], $settings['mail'], $subject, $body);

					// Das Passwort des Benutzers nur abändern, wenn die E-Mail erfolgreich versendet werden konnte
					if ($mail_sent === true) {
						$salt = salt(12);
						$bool = Core\Registry::get('Db')->update(DB_PRE . 'users', array('pwd' => Core\Functions::generateSaltedPassword($salt, $new_password) . ':' . $salt, 'login_errors' => 0), array('id' => $user['id']));
					}

					Core\Registry::get('Session')->unsetFormToken();

					Core\Registry::get('View')->setContent(Core\Functions::confirmBox(Core\Registry::get('Lang')->t('users', $mail_sent === true && isset($bool) && $bool !== false ? 'forgot_pwd_success' : 'forgot_pwd_error'), ROOT_DIR));
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				$defaults = array('nick_mail' => '');

				Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : $defaults);

				if ($captchaAccess === true) {
					Core\Registry::get('View')->assign('captcha', \ACP3\Modules\Captcha\CaptchaFunctions::captcha());
				}

				Core\Registry::get('Session')->generateFormToken();
			}
		}
	}

	public function actionHome()
	{
		if (Core\Registry::get('Auth')->isUser() === false || !Core\Validate::isNumber(Core\Registry::get('Auth')->getUserId())) {
			Core\Registry::get('URI')->redirect('errors/403');
		} else {
			Core\Registry::get('Breadcrumb')
					->append(Core\Registry::get('Lang')->t('users', 'users'), Core\Registry::get('URI')->route('users'))
					->append(Core\Registry::get('Lang')->t('users', 'home'));

			if (isset($_POST['submit']) === true) {
				$bool = Core\Registry::get('Db')->update(DB_PRE . 'users', array('draft' => Core\Functions::strEncode($_POST['draft'], true)), array('id' => Core\Registry::get('Auth')->getUserId()));

				Core\Functions::setRedirectMessage($bool, Core\Registry::get('Lang')->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'users/home');
			}
			if (isset($_POST['submit']) === false) {
				Core\Functions::getRedirectMessage();

				$user_draft = Core\Registry::get('Db')->fetchColumn('SELECT draft FROM ' . DB_PRE . 'users WHERE id = ?', array(Core\Registry::get('Auth')->getUserId()));

				Core\Registry::get('View')->assign('draft', $user_draft);
			}
		}
	}

	public function actionList()
	{
		$users = Core\Registry::get('Db')->fetchAll('SELECT id, nickname, realname, mail, mail_display, website FROM ' . DB_PRE . 'users ORDER BY nickname ASC, id ASC LIMIT ' . POS . ',' . Core\Registry::get('Auth')->entries);
		$c_users = count($users);
		$all_users = Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'users');

		if ($c_users > 0) {
			Core\Registry::get('View')->assign('pagination', Core\Functions::pagination($all_users));

			for ($i = 0; $i < $c_users; ++$i) {
				if (!empty($users[$i]['website']) && (bool) preg_match('=^http(s)?://=', $users[$i]['website']) === false)
					$users[$i]['website'] = 'http://' . $users[$i]['website'];
			}
			Core\Registry::get('View')->assign('users', $users);
		}
		Core\Registry::get('View')->assign('LANG_users_found', sprintf(Core\Registry::get('Lang')->t('users', 'users_found'), $all_users));
	}

	public function actionLogin()
	{
		// Falls der Benutzer schon eingeloggt ist, diesen zur Startseite weiterleiten
		if (Core\Registry::get('Auth')->isUser() === true) {
			Core\Registry::get('URI')->redirect(0, ROOT_DIR);
		} elseif (isset($_POST['submit']) === true) {
			$result = Core\Registry::get('Auth')->login(Core\Functions::strEncode($_POST['nickname']), $_POST['pwd'], isset($_POST['remember']) ? 31104000 : 3600);
			if ($result == 1) {
				if (Core\Registry::get('URI')->redirect) {
					Core\Registry::get('URI')->redirect(base64_decode(Core\Registry::get('URI')->redirect));
				} else {
					Core\Registry::get('URI')->redirect(0, ROOT_DIR);
				}
			} else {
				Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox(Core\Registry::get('Lang')->t('users', $result == -1 ? 'account_locked' : 'nickname_or_password_wrong')));
			}
		}
	}

	public function actionLogout()
	{
		Core\Registry::get('Auth')->logout();

		if (Core\Registry::get('URI')->last) {
			$lastPage = base64_decode(Core\Registry::get('URI')->last);
			if (!preg_match('/^((acp|users)\/)/', $lastPage))
				Core\Registry::get('URI')->redirect($lastPage);
		}
		Core\Registry::get('URI')->redirect(0, ROOT_DIR);
	}

	public function actionRegister()
	{
		$settings = Core\Config::getSettings('users');

		if (Core\Registry::get('Auth')->isUser() === true) {
			Core\Registry::get('URI')->redirect(0, ROOT_DIR);
		} elseif ($settings['enable_registration'] == 0) {
			Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('users', 'user_registration_disabled')));
		} else {
			Core\Registry::get('Breadcrumb')
					->append(Core\Registry::get('Lang')->t('users', 'users'), Core\Registry::get('URI')->route('users'))
					->append(Core\Registry::get('Lang')->t('users', 'register'));

			$captchaAccess = Core\Modules::hasPermission('captcha', 'image');

			if (isset($_POST['submit']) === true) {
				if (empty($_POST['nickname']))
					$errors['nickname'] = Core\Registry::get('Lang')->t('system', 'name_to_short');
				if (UsersFunctions::userNameExists($_POST['nickname']) === true)
					$errors['nickname'] = Core\Registry::get('Lang')->t('users', 'user_name_already_exists');
				if (Core\Validate::email($_POST['mail']) === false)
					$errors['mail'] = Core\Registry::get('Lang')->t('system', 'wrong_email_format');
				if (UsersFunctions::userEmailExists($_POST['mail']) === true)
					$errors['mail'] = Core\Registry::get('Lang')->t('users', 'user_email_already_exists');
				if (empty($_POST['pwd']) || empty($_POST['pwd_repeat']) || $_POST['pwd'] != $_POST['pwd_repeat'])
					$errors[] = Core\Registry::get('Lang')->t('users', 'type_in_pwd');
				if ($captchaAccess === true && Core\Validate::captcha($_POST['captcha']) === false)
					$errors['captcha'] = Core\Registry::get('Lang')->t('captcha', 'invalid_captcha_entered');

				if (isset($errors) === true) {
					Core\Registry::get('View')->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					Core\Registry::get('View')->setContent(Core\Functions::errorBox(Core\Registry::get('Lang')->t('system', 'form_already_submitted')));
				} else {
					// E-Mail mit den Accountdaten zusenden
					$host = htmlentities($_SERVER['HTTP_HOST']);
					$subject = str_replace(array('{title}', '{host}'), array(CONFIG_SEO_TITLE, $host), Core\Registry::get('Lang')->t('users', 'register_mail_subject'));
					$body = str_replace(array('{name}', '{mail}', '{password}', '{title}', '{host}'), array($_POST['nickname'], $_POST['mail'], $_POST['pwd'], CONFIG_SEO_TITLE, $host), Core\Registry::get('Lang')->t('users', 'register_mail_message'));
					$mail_sent = Core\Functions::generateEmail('', $_POST['mail'], $settings['mail'], $subject, $body);

					$salt = salt(12);
					$insert_values = array(
						'id' => '',
						'nickname' => Core\Functions::strEncode($_POST['nickname']),
						'pwd' => Core\Functions::generateSaltedPassword($salt, $_POST['pwd']) . ':' . $salt,
						'mail' => $_POST['mail'],
						'date_format_long' => CONFIG_DATE_FORMAT_LONG,
						'date_format_short' => CONFIG_DATE_FORMAT_SHORT,
						'time_zone' => CONFIG_DATE_TIME_ZONE,
						'language' => CONFIG_LANG,
						'entries' => CONFIG_ENTRIES,
					);

					Core\Registry::get('Db')->beginTransaction();
					try {
						$bool = Core\Registry::get('Db')->insert(DB_PRE . 'users', $insert_values);
						$user_id = Core\Registry::get('Db')->lastInsertId();
						$bool2 = Core\Registry::get('Db')->insert(DB_PRE . 'acl_user_roles', array('user_id' => $user_id, 'role_id' => 2));
						Core\Registry::get('Db')->commit();
					} catch (\Exception $e) {
						Core\Registry::get('Db')->rollback();
						$bool = $bool2 = false;
					}

					Core\Registry::get('Session')->unsetFormToken();

					Core\Registry::get('View')->setContent(Core\Functions::confirmBox(Core\Registry::get('Lang')->t('users', $mail_sent === true && $bool !== false && $bool2 !== false ? 'register_success' : 'register_error'), ROOT_DIR));
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				$defaults = array(
					'nickname' => '',
					'mail' => '',
				);

				Core\Registry::get('View')->assign('form', isset($_POST['submit']) ? $_POST : $defaults);

				if ($captchaAccess === true) {
					Core\Registry::get('View')->assign('captcha', \ACP3\Modules\Captcha\CaptchaFunctions::captcha());
				}

				Core\Registry::get('Session')->generateFormToken();
			}
		}
	}

	public function actionViewProfile()
	{
		Core\Registry::get('Breadcrumb')
				->append(Core\Registry::get('Lang')->t('users', 'users'), Core\Registry::get('URI')->route('users'))
				->append(Core\Registry::get('Lang')->t('users', 'view_profile'));

		if (Core\Validate::isNumber(Core\Registry::get('URI')->id) === true &&
				Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'users WHERE id = ?', array(Core\Registry::get('URI')->id)) == 1) {
			$user = Core\Registry::get('Auth')->getUserInfo(Core\Registry::get('URI')->id);
			$user['gender'] = str_replace(array(1, 2, 3), array('', Core\Registry::get('Lang')->t('users', 'female'), Core\Registry::get('Lang')->t('users', 'male')), $user['gender']);
			$user['birthday'] = Core\Registry::get('Date')->format($user['birthday'], $user['birthday_display'] == 1 ? 'd.m.Y' : 'd.m.');
			if (!empty($user['website']) && (bool) preg_match('=^http(s)?://=', $user['website']) === false)
				$user['website'] = 'http://' . $user['website'];

			Core\Registry::get('View')->assign('user', $user);
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionSidebar()
	{
		$currentPage = base64_encode((defined('IN_ADM') === true ? 'acp/' : '') . Core\Registry::get('URI')->query);

		// Usermenü anzeigen, falls der Benutzer eingeloggt ist
		if (Core\Registry::get('Auth')->isUser() === true) {
			$user_sidebar = array();
			$user_sidebar['page'] = $currentPage;

			// Module holen
			$mod_list = Core\Modules::getActiveModules();
			$nav_mods = $nav_system = array();
			$access_system = false;

			foreach ($mod_list as $name => $info) {
				$dir = strtolower($info['dir']);
				if ($dir !== 'acp' && Core\Modules::hasPermission($dir, 'acp_list') === true) {
					if ($dir === 'system') {
						$access_system = true;
					} else {
						$nav_mods[$name]['name'] = $name;
						$nav_mods[$name]['dir'] = $dir;
						$nav_mods[$name]['active'] = defined('IN_ADM') === true && $dir === Core\Registry::get('URI')->mod ? ' class="active"' : '';
					}
				}
			}
			if (!empty($nav_mods)) {
				$user_sidebar['modules'] = $nav_mods;
			}

			if ($access_system === true) {
				$i = 0;
				if (Core\Modules::hasPermission('system', 'acp_configuration') === true) {
					$nav_system[$i]['page'] = 'configuration';
					$nav_system[$i]['name'] = Core\Registry::get('Lang')->t('system', 'acp_configuration');
					$nav_system[$i]['active'] = Core\Registry::get('URI')->query === 'system/configuration/' ? ' class="active"' : '';
				}
				if (Core\Modules::hasPermission('system', 'acp_extensions') === true) {
					$i++;
					$nav_system[$i]['page'] = 'extensions';
					$nav_system[$i]['name'] = Core\Registry::get('Lang')->t('system', 'acp_extensions');
					$nav_system[$i]['active'] = Core\Registry::get('URI')->query === 'system/extensions/' ? ' class="active"' : '';
				}
				if (Core\Modules::hasPermission('system', 'acp_maintenance') === true) {
					$i++;
					$nav_system[$i]['page'] = 'maintenance';
					$nav_system[$i]['name'] = Core\Registry::get('Lang')->t('system', 'acp_maintenance');
					$nav_system[$i]['active'] = Core\Registry::get('URI')->query === 'system/maintenance/' ? ' class="active"' : '';
				}
				$user_sidebar['system'] = $nav_system;
			}

			Core\Registry::get('View')->assign('user_sidebar', $user_sidebar);

			Core\Registry::get('View')->displayTemplate('users/sidebar_user_menu.tpl');
		} else {
			$settings = Core\Config::getSettings('users');

			Core\Registry::get('View')->assign('enable_registration', $settings['enable_registration']);
			Core\Registry::get('View')->assign('redirect_uri', isset($_POST['redirect_uri']) ? $_POST['redirect_uri'] : $currentPage);

			Core\Registry::get('View')->displayTemplate('users/sidebar_login.tpl');
		}
	}

}