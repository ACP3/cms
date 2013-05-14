<?php

namespace ACP3\Modules\Users;

use ACP3\Core;

/**
 * Description of UsersFrontend
 *
 * @author Tino
 */
class UsersFrontend extends Core\ModuleController {

	public function __construct($injector)
	{
		parent::__construct($injector);
	}

	public function actionEdit_profile()
	{
		if ($this->injector['Auth']->isUser() === false || Core\Validate::isNumber($this->injector['Auth']->getUserId()) === false) {
			$this->injector['URI']->redirect('errors/403');
		} else {
			$this->injector['Breadcrumb']
					->append($this->injector['Lang']->t('users', 'users'), $this->injector['URI']->route('users'))
					->append($this->injector['Lang']->t('users', 'home'), $this->injector['URI']->route('users/home'))
					->append($this->injector['Lang']->t('users', 'edit_profile'));

			if (isset($_POST['submit']) === true) {
				if (empty($_POST['nickname']))
					$errors['nnickname'] = $this->injector['Lang']->t('system', 'name_to_short');
				if (UsersFunctions::userNameExists($_POST['nickname'], $this->injector['Auth']->getUserId()) === true)
					$errors['nickname'] = $this->injector['Lang']->t('users', 'user_name_already_exists');
				if (Core\Validate::gender($_POST['gender']) === false)
					$errors['gender'] = $this->injector['Lang']->t('users', 'select_gender');
				if (!empty($_POST['birthday']) && Core\Validate::birthday($_POST['birthday']) === false)
					$errors[] = $this->injector['Lang']->t('users', 'invalid_birthday');
				if (Core\Validate::email($_POST['mail']) === false)
					$errors['mail'] = $this->injector['Lang']->t('system', 'wrong_email_format');
				if (UsersFunctions::userEmailExists($_POST['mail'], $this->injector['Auth']->getUserId()) === true)
					$errors['mail'] = $this->injector['Lang']->t('users', 'user_email_already_exists');
				if (!empty($_POST['icq']) && Core\Validate::icq($_POST['icq']) === false)
					$errors['icq'] = $this->injector['Lang']->t('users', 'invalid_icq_number');
				if (!empty($_POST['new_pwd']) && !empty($_POST['new_pwd_repeat']) && $_POST['new_pwd'] != $_POST['new_pwd_repeat'])
					$errors[] = $this->injector['Lang']->t('users', 'type_in_pwd');

				if (isset($errors) === true) {
					$this->injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'form_already_submitted')));
				} else {
					$update_values = array(
						'nickname' => Core\Functions::str_encode($_POST['nickname']),
						'realname' => Core\Functions::str_encode($_POST['realname']),
						'gender' => (int) $_POST['gender'],
						'birthday' => $_POST['birthday'],
						'mail' => $_POST['mail'],
						'website' => Core\Functions::str_encode($_POST['website']),
						'icq' => $_POST['icq'],
						'skype' => Core\Functions::str_encode($_POST['skype']),
						'street' => Core\Functions::str_encode($_POST['street']),
						'house_number' => Core\Functions::str_encode($_POST['house_number']),
						'zip' => Core\Functions::str_encode($_POST['zip']),
						'city' => Core\Functions::str_encode($_POST['city']),
						'country' => Core\Functions::str_encode($_POST['country']),
					);

					// Neues Passwort
					if (!empty($_POST['new_pwd']) && !empty($_POST['new_pwd_repeat'])) {
						$salt = salt(12);
						$new_pwd = Core\Functions::generateSaltedPassword($salt, $_POST['new_pwd']);
						$update_values['pwd'] = $new_pwd . ':' . $salt;
					}

					$bool = $this->injector['Db']->update(DB_PRE . 'users', $update_values, array('id' => $this->injector['Auth']->getUserId()));

					$cookie_arr = explode('|', base64_decode($_COOKIE['ACP3_AUTH']));
					$this->injector['Auth']->setCookie($_POST['nickname'], isset($new_pwd) ? $new_pwd : $cookie_arr[1], 3600);

					$this->injector['Session']->unsetFormToken();

					Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'users/home');
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				$user = $this->injector['Auth']->getUserInfo();

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

				$countries = Core\Lang::worldCountries();
				$countries_select = array();
				foreach ($countries as $key => $value) {
					$countries_select[] = array(
						'value' => $key,
						'lang' => $value,
						'selected' => Core\Functions::selectEntry('countries', $key, $user['country']),
					);
				}
				$this->injector['View']->assign('countries', $countries_select);

				$this->injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $user);

				$this->injector['Session']->generateFormToken();
			}
		}
	}

	public function actionEdit_settings()
	{
		if ($this->injector['Auth']->isUser() === false || Core\Validate::isNumber($this->injector['Auth']->getUserId()) === false) {
			$this->injector['URI']->redirect('errors/403');
		} else {
			$settings = Core\Config::getSettings('users');

			$this->injector['Breadcrumb']
					->append($this->injector['Lang']->t('users', 'users'), $this->injector['URI']->route('users'))
					->append($this->injector['Lang']->t('users', 'home'), $this->injector['URI']->route('users/home'))
					->append($this->injector['Lang']->t('users', 'edit_settings'));

			if (isset($_POST['submit']) === true) {
				if ($settings['language_override'] == 1 && $this->injector['Lang']->languagePackExists($_POST['language']) === false)
					$errors['language'] = $this->injector['Lang']->t('users', 'select_language');
				if ($settings['entries_override'] == 1 && Core\Validate::isNumber($_POST['entries']) === false)
					$errors['entries'] = $this->injector['Lang']->t('system', 'select_records_per_page');
				if (empty($_POST['date_format_long']) || empty($_POST['date_format_short']))
					$errors[] = $this->injector['Lang']->t('system', 'type_in_date_format');
				if (Core\Validate::timeZone($_POST['date_time_zone']) === false)
					$errors['time-zone'] = $this->injector['Lang']->t('system', 'select_time_zone');
				if (in_array($_POST['mail_display'], array(0, 1)) === false)
					$errors[] = $this->injector['Lang']->t('users', 'select_mail_display');
				if (in_array($_POST['address_display'], array(0, 1)) === false)
					$errors[] = $this->injector['Lang']->t('users', 'select_address_display');
				if (in_array($_POST['country_display'], array(0, 1)) === false)
					$errors[] = $this->injector['Lang']->t('users', 'select_country_display');
				if (in_array($_POST['birthday_display'], array(0, 1, 2)) === false)
					$errors[] = $this->injector['Lang']->t('users', 'select_birthday_display');

				if (isset($errors) === true) {
					$this->injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'form_already_submitted')));
				} else {
					$update_values = array(
						'mail_display' => (int) $_POST['mail_display'],
						'birthday_display' => (int) $_POST['birthday_display'],
						'address_display' => (int) $_POST['address_display'],
						'country_display' => (int) $_POST['country_display'],
						'date_format_long' => Core\Functions::str_encode($_POST['date_format_long']),
						'date_format_short' => Core\Functions::str_encode($_POST['date_format_short']),
						'time_zone' => $_POST['date_time_zone'],
					);
					if ($settings['language_override'] == 1)
						$update_values['language'] = $_POST['language'];
					if ($settings['entries_override'] == 1)
						$update_values['entries'] = (int) $_POST['entries'];

					$bool = $this->injector['Db']->update(DB_PRE . 'users', $update_values, array('id' => $this->injector['Auth']->getUserId()));

					$this->injector['Session']->unsetFormToken();

					Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool !== false ? 'settings_success' : 'settings_error'), 'users/home');
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				$user = $this->injector['Db']->fetchAssoc('SELECT mail_display, birthday_display, address_display, country_display, date_format_long, date_format_short, time_zone, language, entries FROM ' . DB_PRE . 'users WHERE id = ?', array($this->injector['Auth']->getUserId()));

				$this->injector['View']->assign('language_override', $settings['language_override']);
				$this->injector['View']->assign('entries_override', $settings['entries_override']);

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
		}
	}

	public function actionForgot_pwd()
	{
		if ($this->injector['Auth']->isUser() === true) {
			$this->injector['URI']->redirect(0, ROOT_DIR);
		} else {
			$this->injector['Breadcrumb']
					->append($this->injector['Lang']->t('users', 'users'), $this->injector['URI']->route('users'))
					->append($this->injector['Lang']->t('users', 'forgot_pwd'));

			$captchaAccess = Core\Modules::isActive('captcha');

			if (isset($_POST['submit']) === true) {
				if (empty($_POST['nick_mail']))
					$errors['nick-mail'] = $this->injector['Lang']->t('users', 'type_in_nickname_or_email');
				elseif (Core\Validate::email($_POST['nick_mail']) === false && UsersFunctions::userNameExists($_POST['nick_mail']) === false)
					$errors['nick-mail'] = $this->injector['Lang']->t('users', 'user_not_exists');
				elseif (Core\Validate::email($_POST['nick_mail']) === true && UsersFunctions::userEmailExists($_POST['nick_mail']) === false)
					$errors['nick-mail'] = $this->injector['Lang']->t('users', 'user_not_exists');
				if ($captchaAccess === true && Core\Validate::captcha($_POST['captcha']) === false)
					$errors['captcha'] = $this->injector['Lang']->t('captcha', 'invalid_captcha_entered');

				if (isset($errors) === true) {
					$this->injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'form_already_submitted')));
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
					$user = $this->injector['Db']->fetchAssoc($query, array($_POST['nick_mail']));

					// E-Mail mit dem neuen Passwort versenden
					$subject = str_replace(array('{title}', '{host}'), array(CONFIG_SEO_TITLE, $host), $this->injector['Lang']->t('users', 'forgot_pwd_mail_subject'));
					$search = array('{name}', '{mail}', '{password}', '{title}', '{host}');
					$replace = array($user['nickname'], $user['mail'], $new_password, CONFIG_SEO_TITLE, $host);
					$body = str_replace($search, $replace, $this->injector['Lang']->t('users', 'forgot_pwd_mail_message'));

					$settings = Core\Config::getSettings('users');
					$mail_sent = Core\Functions::generateEmail(substr($user['realname'], 0, -2), $user['mail'], $settings['mail'], $subject, $body);

					// Das Passwort des Benutzers nur abändern, wenn die E-Mail erfolgreich versendet werden konnte
					if ($mail_sent === true) {
						$salt = salt(12);
						$bool = $this->injector['Db']->update(DB_PRE . 'users', array('pwd' => Core\Functions::generateSaltedPassword($salt, $new_password) . ':' . $salt, 'login_errors' => 0), array('id' => $user['id']));
					}

					$this->injector['Session']->unsetFormToken();

					$this->injector['View']->setContent(Core\Functions::confirmBox($this->injector['Lang']->t('users', $mail_sent === true && isset($bool) && $bool !== false ? 'forgot_pwd_success' : 'forgot_pwd_error'), ROOT_DIR));
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				$defaults = array('nick_mail' => '');

				$this->injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $defaults);

				if ($captchaAccess === true) {
					$this->injector['View']->assign('captcha', \ACP3\Modules\Captcha\CaptchaFunctions::captcha());
				}

				$this->injector['Session']->generateFormToken();
			}
		}
	}

	public function actionHome()
	{
		if ($this->injector['Auth']->isUser() === false || !Core\Validate::isNumber($this->injector['Auth']->getUserId())) {
			$this->injector['URI']->redirect('errors/403');
		} else {
			$this->injector['Breadcrumb']
					->append($this->injector['Lang']->t('users', 'users'), $this->injector['URI']->route('users'))
					->append($this->injector['Lang']->t('users', 'home'));

			if (isset($_POST['submit']) === true) {
				$bool = $this->injector['Db']->update(DB_PRE . 'users', array('draft' => Core\Functions::str_encode($_POST['draft'], true)), array('id' => $this->injector['Auth']->getUserId()));

				Core\Functions::setRedirectMessage($bool, $this->injector['Lang']->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'users/home');
			}
			if (isset($_POST['submit']) === false) {
				Core\Functions::getRedirectMessage();

				$user_draft = $this->injector['Db']->fetchColumn('SELECT draft FROM ' . DB_PRE . 'users WHERE id = ?', array($this->injector['Auth']->getUserId()));

				$this->injector['View']->assign('draft', $user_draft);
			}
		}
	}

	public function actionList()
	{
		$users = $this->injector['Db']->fetchAll('SELECT id, nickname, realname, mail, mail_display, website FROM ' . DB_PRE . 'users ORDER BY nickname ASC, id ASC LIMIT ' . POS . ',' . $this->injector['Auth']->entries);
		$c_users = count($users);
		$all_users = $this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'users');

		if ($c_users > 0) {
			$this->injector['View']->assign('pagination', Core\Functions::pagination($all_users));

			for ($i = 0; $i < $c_users; ++$i) {
				if (!empty($users[$i]['website']) && (bool) preg_match('=^http(s)?://=', $users[$i]['website']) === false)
					$users[$i]['website'] = 'http://' . $users[$i]['website'];
			}
			$this->injector['View']->assign('users', $users);
		}
		$this->injector['View']->assign('LANG_users_found', sprintf($this->injector['Lang']->t('users', 'users_found'), $all_users));
	}

	public function actionLogin()
	{
		// Falls der Benutzer schon eingeloggt ist, diesen zur Startseite weiterleiten
		if ($this->injector['Auth']->isUser() === true) {
			$this->injector['URI']->redirect(0, ROOT_DIR);
		} elseif (isset($_POST['submit']) === true) {
			$result = $this->injector['Auth']->login(Core\Functions::str_encode($_POST['nickname']), $_POST['pwd'], isset($_POST['remember']) ? 31104000 : 3600);
			if ($result == 1) {
				if ($this->injector['URI']->redirect) {
					$this->injector['URI']->redirect(base64_decode($this->injector['URI']->redirect));
				} else {
					$this->injector['URI']->redirect(0, ROOT_DIR);
				}
			} else {
				$this->injector['View']->assign('error_msg', Core\Functions::errorBox($this->injector['Lang']->t('users', $result == -1 ? 'account_locked' : 'nickname_or_password_wrong')));
			}
		}
	}

	public function actionLogout()
	{
		$this->injector['Auth']->logout();

		if ($this->injector['URI']->last) {
			$lastPage = base64_decode($this->injector['URI']->last);
			if (!preg_match('/^((acp|users)\/)/', $lastPage))
				$this->injector['URI']->redirect($lastPage);
		}
		$this->injector['URI']->redirect(0, ROOT_DIR);
	}

	public function actionRegister()
	{
		$settings = Core\Config::getSettings('users');

		if ($this->injector['Auth']->isUser() === true) {
			$this->injector['URI']->redirect(0, ROOT_DIR);
		} elseif ($settings['enable_registration'] == 0) {
			$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('users', 'user_registration_disabled')));
		} else {
			$this->injector['Breadcrumb']
					->append($this->injector['Lang']->t('users', 'users'), $this->injector['URI']->route('users'))
					->append($this->injector['Lang']->t('users', 'register'));

			$captchaAccess = Core\Modules::isActive('captcha');

			if (isset($_POST['submit']) === true) {
				if (empty($_POST['nickname']))
					$errors['nickname'] = $this->injector['Lang']->t('system', 'name_to_short');
				if (UsersFunctions::userNameExists($_POST['nickname']) === true)
					$errors['nickname'] = $this->injector['Lang']->t('users', 'user_name_already_exists');
				if (Core\Validate::email($_POST['mail']) === false)
					$errors['mail'] = $this->injector['Lang']->t('system', 'wrong_email_format');
				if (UsersFunctions::userEmailExists($_POST['mail']) === true)
					$errors['mail'] = $this->injector['Lang']->t('users', 'user_email_already_exists');
				if (empty($_POST['pwd']) || empty($_POST['pwd_repeat']) || $_POST['pwd'] != $_POST['pwd_repeat'])
					$errors[] = $this->injector['Lang']->t('users', 'type_in_pwd');
				if ($captchaAccess === true && Core\Validate::captcha($_POST['captcha']) === false)
					$errors['captcha'] = $this->injector['Lang']->t('captcha', 'invalid_captcha_entered');

				if (isset($errors) === true) {
					$this->injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
				} elseif (Core\Validate::formToken() === false) {
					$this->injector['View']->setContent(Core\Functions::errorBox($this->injector['Lang']->t('system', 'form_already_submitted')));
				} else {
					// E-Mail mit den Accountdaten zusenden
					$host = htmlentities($_SERVER['HTTP_HOST']);
					$subject = str_replace(array('{title}', '{host}'), array(CONFIG_SEO_TITLE, $host), $this->injector['Lang']->t('users', 'register_mail_subject'));
					$body = str_replace(array('{name}', '{mail}', '{password}', '{title}', '{host}'), array($_POST['nickname'], $_POST['mail'], $_POST['pwd'], CONFIG_SEO_TITLE, $host), $this->injector['Lang']->t('users', 'register_mail_message'));
					$mail_sent = Core\Functions::generateEmail('', $_POST['mail'], $subject, $body);

					$salt = salt(12);
					$insert_values = array(
						'id' => '',
						'nickname' => Core\Functions::str_encode($_POST['nickname']),
						'pwd' => Core\Functions::generateSaltedPassword($salt, $_POST['pwd']) . ':' . $salt,
						'mail' => $_POST['mail'],
						'date_format_long' => CONFIG_DATE_FORMAT_LONG,
						'date_format_short' => CONFIG_DATE_FORMAT_SHORT,
						'time_zone' => CONFIG_DATE_TIME_ZONE,
						'language' => CONFIG_LANG,
						'entries' => CONFIG_ENTRIES,
					);

					$this->injector['Db']->beginTransaction();
					try {
						$bool = $this->injector['Db']->insert(DB_PRE . 'users', $insert_values);
						$user_id = $this->injector['Db']->lastInsertId();
						$bool2 = $this->injector['Db']->insert(DB_PRE . 'acl_user_roles', array('user_id' => $user_id, 'role_id' => 2));
						$this->injector['Db']->commit();
					} catch (\Exception $e) {
						$this->injector['Db']->rollback();
						$bool = $bool2 = false;
					}

					$this->injector['Session']->unsetFormToken();

					$this->injector['View']->setContent(Core\Functions::confirmBox($this->injector['Lang']->t('users', $mail_sent === true && $bool !== false && $bool2 !== false ? 'register_success' : 'register_error'), ROOT_DIR));
				}
			}
			if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
				$defaults = array(
					'nickname' => '',
					'mail' => '',
				);

				$this->injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $defaults);

				if ($captchaAccess === true) {
					$this->injector['View']->assign('captcha', \ACP3\Modules\Captcha\CaptchaFunctions::captcha());
				}

				$this->injector['Session']->generateFormToken();
			}
		}
	}

	public function actionView_profile()
	{
		$this->injector['Breadcrumb']
				->append($this->injector['Lang']->t('users', 'users'), $this->injector['URI']->route('users'))
				->append($this->injector['Lang']->t('users', 'view_profile'));

		if (Core\Validate::isNumber($this->injector['URI']->id) === true &&
				$this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'users WHERE id = ?', array($this->injector['URI']->id)) == 1) {
			$user = $this->injector['Auth']->getUserInfo($this->injector['URI']->id);
			$user['gender'] = str_replace(array(1, 2, 3), array('', $this->injector['Lang']->t('users', 'female'), $this->injector['Lang']->t('users', 'male')), $user['gender']);
			$user['birthday'] = $this->injector['Date']->format($user['birthday'], $user['birthday_display'] == 1 ? 'd.m.Y' : 'd.m.');
			if (!empty($user['website']) && (bool) preg_match('=^http(s)?://=', $user['website']) === false)
				$user['website'] = 'http://' . $user['website'];

			$this->injector['View']->assign('user', $user);
		} else {
			$this->injector['URI']->redirect('errors/404');
		}
	}

	public function actionSidebar()
	{
		$currentPage = base64_encode((defined('IN_ADM') === true ? 'acp/' : '') . $this->injector['URI']->query);

		// Usermenü anzeigen, falls der Benutzer eingeloggt ist
		if ($this->injector['Auth']->isUser() === true) {
			$user_sidebar = array();
			$user_sidebar['page'] = $currentPage;

			// Module holen
			$mod_list = Core\Modules::getActiveModules();
			$nav_mods = $nav_system = array();
			$access_system = false;

			foreach ($mod_list as $name => $info) {
				$dir = strtolower($info['dir']);
				if ($dir !== 'acp' && Core\Modules::check($dir, 'acp_list') === true) {
					if ($dir === 'system') {
						$access_system = true;
					} else {
						$nav_mods[$name]['name'] = $name;
						$nav_mods[$name]['dir'] = $dir;
						$nav_mods[$name]['active'] = defined('IN_ADM') === true && $dir === $this->injector['URI']->mod ? ' class="active"' : '';
					}
				}
			}
			if (!empty($nav_mods)) {
				$user_sidebar['modules'] = $nav_mods;
			}

			if ($access_system === true) {
				$i = 0;
				if (Core\Modules::check('system', 'acp_configuration') === true) {
					$nav_system[$i]['page'] = 'configuration';
					$nav_system[$i]['name'] = $this->injector['Lang']->t('system', 'acp_configuration');
					$nav_system[$i]['active'] = $this->injector['URI']->query === 'system/configuration/' ? ' class="active"' : '';
				}
				if (Core\Modules::check('system', 'acp_extensions') === true) {
					$i++;
					$nav_system[$i]['page'] = 'extensions';
					$nav_system[$i]['name'] = $this->injector['Lang']->t('system', 'acp_extensions');
					$nav_system[$i]['active'] = $this->injector['URI']->query === 'system/extensions/' ? ' class="active"' : '';
				}
				if (Core\Modules::check('system', 'acp_maintenance') === true) {
					$i++;
					$nav_system[$i]['page'] = 'maintenance';
					$nav_system[$i]['name'] = $this->injector['Lang']->t('system', 'acp_maintenance');
					$nav_system[$i]['active'] = $this->injector['URI']->query === 'system/maintenance/' ? ' class="active"' : '';
				}
				$user_sidebar['system'] = $nav_system;
			}

			$this->injector['View']->assign('user_sidebar', $user_sidebar);

			$this->injector['View']->displayTemplate('users/sidebar_user_menu.tpl');
		} else {
			$settings = Core\Config::getSettings('users');

			$this->injector['View']->assign('enable_registration', $settings['enable_registration']);
			$this->injector['View']->assign('redirect_uri', isset($_POST['redirect_uri']) ? $_POST['redirect_uri'] : $currentPage);

			$this->injector['View']->displayTemplate('users/sidebar_login.tpl');
		}
	}

}