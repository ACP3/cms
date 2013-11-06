<?php

namespace ACP3\Modules\Users;

use ACP3\Core;

/**
 * Description of UsersFrontend
 *
 * @author Tino Goratsch
 */
class Frontend extends Core\Modules\Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function actionEditProfile()
    {
        if ($this->auth->isUser() === false || Core\Validate::isNumber($this->auth->getUserId()) === false) {
            $this->uri->redirect('errors/403');
        } else {
            $this->breadcrumb
                ->append($this->lang->t('users', 'users'), $this->uri->route('users'))
                ->append($this->lang->t('users', 'home'), $this->uri->route('users/home'))
                ->append($this->lang->t('users', 'edit_profile'));

            if (isset($_POST['submit']) === true) {
                if (empty($_POST['nickname']))
                    $errors['nnickname'] = $this->lang->t('system', 'name_to_short');
                if (Helpers::userNameExists($_POST['nickname'], $this->auth->getUserId()) === true)
                    $errors['nickname'] = $this->lang->t('users', 'user_name_already_exists');
                if (Core\Validate::gender($_POST['gender']) === false)
                    $errors['gender'] = $this->lang->t('users', 'select_gender');
                if (!empty($_POST['birthday']) && Core\Validate::birthday($_POST['birthday']) === false)
                    $errors[] = $this->lang->t('users', 'invalid_birthday');
                if (Core\Validate::email($_POST['mail']) === false)
                    $errors['mail'] = $this->lang->t('system', 'wrong_email_format');
                if (Helpers::userEmailExists($_POST['mail'], $this->auth->getUserId()) === true)
                    $errors['mail'] = $this->lang->t('users', 'user_email_already_exists');
                if (!empty($_POST['icq']) && Core\Validate::icq($_POST['icq']) === false)
                    $errors['icq'] = $this->lang->t('users', 'invalid_icq_number');
                if (!empty($_POST['new_pwd']) && !empty($_POST['new_pwd_repeat']) && $_POST['new_pwd'] != $_POST['new_pwd_repeat'])
                    $errors[] = $this->lang->t('users', 'type_in_pwd');

                if (isset($errors) === true) {
                    $this->view->assign('error_msg', Core\Functions::errorBox($errors));
                } elseif (Core\Validate::formToken() === false) {
                    $this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
                } else {
                    $update_values = array(
                        'nickname' => Core\Functions::strEncode($_POST['nickname']),
                        'realname' => Core\Functions::strEncode($_POST['realname']),
                        'gender' => (int)$_POST['gender'],
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
                        $salt = Core\Functions::salt(12);
                        $new_pwd = Core\Functions::generateSaltedPassword($salt, $_POST['new_pwd']);
                        $update_values['pwd'] = $new_pwd . ':' . $salt;
                    }

                    $bool = $this->db->update(DB_PRE . 'users', $update_values, array('id' => $this->auth->getUserId()));

                    $cookie_arr = explode('|', base64_decode($_COOKIE['ACP3_AUTH']));
                    $this->auth->setCookie($_POST['nickname'], isset($new_pwd) ? $new_pwd : $cookie_arr[1], 3600);

                    $this->session->unsetFormToken();

                    Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'users/home');
                }
            }
            if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
                $user = $this->auth->getUserInfo();

                // Geschlecht
                $lang_gender = array(
                    $this->lang->t('users', 'gender_not_specified'),
                    $this->lang->t('users', 'gender_female'),
                    $this->lang->t('users', 'gender_male')
                );
                $this->view->assign('gender', Core\Functions::selectGenerator('gender', array(1, 2, 3), $lang_gender, $user['gender']));

                // Geburtstag
                $datepickerParams = array('constrainInput' => 'true', 'changeMonth' => 'true', 'changeYear' => 'true', 'yearRange' => '\'-50:+0\'');
                $this->view->assign('birthday_datepicker', $this->date->datepicker('birthday', $user['birthday'], 'Y-m-d', $datepickerParams, 0, false, true));

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

                $this->view->assign('form', isset($_POST['submit']) ? $_POST : $user);

                $this->session->generateFormToken();
            }
        }
    }

    public function actionEditSettings()
    {
        if ($this->auth->isUser() === false || Core\Validate::isNumber($this->auth->getUserId()) === false) {
            $this->uri->redirect('errors/403');
        } else {
            $settings = Core\Config::getSettings('users');

            $this->breadcrumb
                ->append($this->lang->t('users', 'users'), $this->uri->route('users'))
                ->append($this->lang->t('users', 'home'), $this->uri->route('users/home'))
                ->append($this->lang->t('users', 'edit_settings'));

            if (isset($_POST['submit']) === true) {
                if ($settings['language_override'] == 1 && $this->lang->languagePackExists($_POST['language']) === false)
                    $errors['language'] = $this->lang->t('users', 'select_language');
                if ($settings['entries_override'] == 1 && Core\Validate::isNumber($_POST['entries']) === false)
                    $errors['entries'] = $this->lang->t('system', 'select_records_per_page');
                if (empty($_POST['date_format_long']) || empty($_POST['date_format_short']))
                    $errors[] = $this->lang->t('system', 'type_in_date_format');
                if (Core\Validate::timeZone($_POST['date_time_zone']) === false)
                    $errors['time-zone'] = $this->lang->t('system', 'select_time_zone');
                if (in_array($_POST['mail_display'], array(0, 1)) === false)
                    $errors[] = $this->lang->t('users', 'select_mail_display');
                if (in_array($_POST['address_display'], array(0, 1)) === false)
                    $errors[] = $this->lang->t('users', 'select_address_display');
                if (in_array($_POST['country_display'], array(0, 1)) === false)
                    $errors[] = $this->lang->t('users', 'select_country_display');
                if (in_array($_POST['birthday_display'], array(0, 1, 2)) === false)
                    $errors[] = $this->lang->t('users', 'select_birthday_display');

                if (isset($errors) === true) {
                    $this->view->assign('error_msg', Core\Functions::errorBox($errors));
                } elseif (Core\Validate::formToken() === false) {
                    $this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
                } else {
                    $update_values = array(
                        'mail_display' => (int)$_POST['mail_display'],
                        'birthday_display' => (int)$_POST['birthday_display'],
                        'address_display' => (int)$_POST['address_display'],
                        'country_display' => (int)$_POST['country_display'],
                        'date_format_long' => Core\Functions::strEncode($_POST['date_format_long']),
                        'date_format_short' => Core\Functions::strEncode($_POST['date_format_short']),
                        'time_zone' => $_POST['date_time_zone'],
                    );
                    if ($settings['language_override'] == 1)
                        $update_values['language'] = $_POST['language'];
                    if ($settings['entries_override'] == 1)
                        $update_values['entries'] = (int)$_POST['entries'];

                    $bool = $this->db->update(DB_PRE . 'users', $update_values, array('id' => $this->auth->getUserId()));

                    $this->session->unsetFormToken();

                    Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'settings_success' : 'settings_error'), 'users/home');
                }
            }
            if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
                $user = $this->db->fetchAssoc('SELECT mail_display, birthday_display, address_display, country_display, date_format_long, date_format_short, time_zone, language, entries FROM ' . DB_PRE . 'users WHERE id = ?', array($this->auth->getUserId()));

                $this->view->assign('language_override', $settings['language_override']);
                $this->view->assign('entries_override', $settings['entries_override']);

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
                $this->view->assign('entries', Core\Functions::recordsPerPage((int)$user['entries']));

                // Zeitzonen
                $this->view->assign('time_zones', Core\Date::getTimeZones($user['time_zone']));

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
        }
    }

    public function actionForgotPwd()
    {
        if ($this->auth->isUser() === true) {
            $this->uri->redirect(0, ROOT_DIR);
        } else {
            $this->breadcrumb
                ->append($this->lang->t('users', 'users'), $this->uri->route('users'))
                ->append($this->lang->t('users', 'forgot_pwd'));

            $captchaAccess = Core\Modules::hasPermission('captcha', 'image');

            if (isset($_POST['submit']) === true) {
                if (empty($_POST['nick_mail']))
                    $errors['nick-mail'] = $this->lang->t('users', 'type_in_nickname_or_email');
                elseif (Core\Validate::email($_POST['nick_mail']) === false && Helpers::userNameExists($_POST['nick_mail']) === false)
                    $errors['nick-mail'] = $this->lang->t('users', 'user_not_exists');
                elseif (Core\Validate::email($_POST['nick_mail']) === true && Helpers::userEmailExists($_POST['nick_mail']) === false)
                    $errors['nick-mail'] = $this->lang->t('users', 'user_not_exists');
                if ($captchaAccess === true && Core\Validate::captcha($_POST['captcha']) === false)
                    $errors['captcha'] = $this->lang->t('captcha', 'invalid_captcha_entered');

                if (isset($errors) === true) {
                    $this->view->assign('error_msg', Core\Functions::errorBox($errors));
                } elseif (Core\Validate::formToken() === false) {
                    $this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
                } else {
                    // Neues Passwort und neuen Zufallsschlüssel erstellen
                    $new_password = Core\Functions::salt(8);
                    $host = htmlentities($_SERVER['HTTP_HOST']);

                    // Je nachdem, wie das Feld ausgefüllt wurde, dieses auswählen
                    if (Core\Validate::email($_POST['nick_mail']) === true && Helpers::userEmailExists($_POST['nick_mail']) === true) {
                        $query = 'SELECT id, nickname, realname, mail FROM ' . DB_PRE . 'users WHERE mail = ?';
                    } else {
                        $query = 'SELECT id, nickname, realname, mail FROM ' . DB_PRE . 'users WHERE nickname = ?';
                    }
                    $user = $this->db->fetchAssoc($query, array($_POST['nick_mail']));

                    // E-Mail mit dem neuen Passwort versenden
                    $subject = str_replace(array('{title}', '{host}'), array(CONFIG_SEO_TITLE, $host), $this->lang->t('users', 'forgot_pwd_mail_subject'));
                    $search = array('{name}', '{mail}', '{password}', '{title}', '{host}');
                    $replace = array($user['nickname'], $user['mail'], $new_password, CONFIG_SEO_TITLE, $host);
                    $body = str_replace($search, $replace, $this->lang->t('users', 'forgot_pwd_mail_message'));

                    $settings = Core\Config::getSettings('users');
                    $mail_sent = Core\Functions::generateEmail(substr($user['realname'], 0, -2), $user['mail'], $settings['mail'], $subject, $body);

                    // Das Passwort des Benutzers nur abändern, wenn die E-Mail erfolgreich versendet werden konnte
                    if ($mail_sent === true) {
                        $salt = Core\Functions::salt(12);
                        $bool = $this->db->update(DB_PRE . 'users', array('pwd' => Core\Functions::generateSaltedPassword($salt, $new_password) . ':' . $salt, 'login_errors' => 0), array('id' => $user['id']));
                    }

                    $this->session->unsetFormToken();

                    $this->view->setContent(Core\Functions::confirmBox($this->lang->t('users', $mail_sent === true && isset($bool) && $bool !== false ? 'forgot_pwd_success' : 'forgot_pwd_error'), ROOT_DIR));
                }
            }
            if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
                $defaults = array('nick_mail' => '');

                $this->view->assign('form', isset($_POST['submit']) ? $_POST : $defaults);

                if ($captchaAccess === true) {
                    $this->view->assign('captcha', \ACP3\Modules\Captcha\Helpers::captcha());
                }

                $this->session->generateFormToken();
            }
        }
    }

    public function actionHome()
    {
        if ($this->auth->isUser() === false || !Core\Validate::isNumber($this->auth->getUserId())) {
            $this->uri->redirect('errors/403');
        } else {
            $this->breadcrumb
                ->append($this->lang->t('users', 'users'), $this->uri->route('users'))
                ->append($this->lang->t('users', 'home'));

            if (isset($_POST['submit']) === true) {
                $bool = $this->db->update(DB_PRE . 'users', array('draft' => Core\Functions::strEncode($_POST['draft'], true)), array('id' => $this->auth->getUserId()));

                Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'users/home');
            }
            if (isset($_POST['submit']) === false) {
                Core\Functions::getRedirectMessage();

                $user_draft = $this->db->fetchColumn('SELECT draft FROM ' . DB_PRE . 'users WHERE id = ?', array($this->auth->getUserId()));

                $this->view->assign('draft', $user_draft);
            }
        }
    }

    public function actionList()
    {
        $users = $this->db->fetchAll('SELECT id, nickname, realname, mail, mail_display, website FROM ' . DB_PRE . 'users ORDER BY nickname ASC, id ASC LIMIT ' . POS . ',' . $this->auth->entries);
        $c_users = count($users);
        $all_users = $this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'users');

        if ($c_users > 0) {
            $this->view->assign('pagination', Core\Functions::pagination($all_users));

            for ($i = 0; $i < $c_users; ++$i) {
                if (!empty($users[$i]['website']) && (bool)preg_match('=^http(s)?://=', $users[$i]['website']) === false)
                    $users[$i]['website'] = 'http://' . $users[$i]['website'];
            }
            $this->view->assign('users', $users);
        }
        $this->view->assign('LANG_users_found', sprintf($this->lang->t('users', 'users_found'), $all_users));
    }

    public function actionLogin()
    {
        // Falls der Benutzer schon eingeloggt ist, diesen zur Startseite weiterleiten
        if ($this->auth->isUser() === true) {
            $this->uri->redirect(0, ROOT_DIR);
        } elseif (isset($_POST['submit']) === true) {
            $result = $this->auth->login(Core\Functions::strEncode($_POST['nickname']), $_POST['pwd'], isset($_POST['remember']) ? 31104000 : 3600);
            if ($result == 1) {
                if ($this->uri->redirect) {
                    $this->uri->redirect(base64_decode($this->uri->redirect));
                } else {
                    $this->uri->redirect(0, ROOT_DIR);
                }
            } else {
                $this->view->assign('error_msg', Core\Functions::errorBox($this->lang->t('users', $result == -1 ? 'account_locked' : 'nickname_or_password_wrong')));
            }
        }
    }

    public function actionLogout()
    {
        $this->auth->logout();

        if ($this->uri->last) {
            $lastPage = base64_decode($this->uri->last);
            if (!preg_match('/^((acp|users)\/)/', $lastPage))
                $this->uri->redirect($lastPage);
        }
        $this->uri->redirect(0, ROOT_DIR);
    }

    public function actionRegister()
    {
        $settings = Core\Config::getSettings('users');

        if ($this->auth->isUser() === true) {
            $this->uri->redirect(0, ROOT_DIR);
        } elseif ($settings['enable_registration'] == 0) {
            $this->view->setContent(Core\Functions::errorBox($this->lang->t('users', 'user_registration_disabled')));
        } else {
            $this->breadcrumb
                ->append($this->lang->t('users', 'users'), $this->uri->route('users'))
                ->append($this->lang->t('users', 'register'));

            $captchaAccess = Core\Modules::hasPermission('captcha', 'image');

            if (isset($_POST['submit']) === true) {
                if (empty($_POST['nickname']))
                    $errors['nickname'] = $this->lang->t('system', 'name_to_short');
                if (Helpers::userNameExists($_POST['nickname']) === true)
                    $errors['nickname'] = $this->lang->t('users', 'user_name_already_exists');
                if (Core\Validate::email($_POST['mail']) === false)
                    $errors['mail'] = $this->lang->t('system', 'wrong_email_format');
                if (Helpers::userEmailExists($_POST['mail']) === true)
                    $errors['mail'] = $this->lang->t('users', 'user_email_already_exists');
                if (empty($_POST['pwd']) || empty($_POST['pwd_repeat']) || $_POST['pwd'] != $_POST['pwd_repeat'])
                    $errors[] = $this->lang->t('users', 'type_in_pwd');
                if ($captchaAccess === true && Core\Validate::captcha($_POST['captcha']) === false)
                    $errors['captcha'] = $this->lang->t('captcha', 'invalid_captcha_entered');

                if (isset($errors) === true) {
                    $this->view->assign('error_msg', Core\Functions::errorBox($errors));
                } elseif (Core\Validate::formToken() === false) {
                    $this->view->setContent(Core\Functions::errorBox($this->lang->t('system', 'form_already_submitted')));
                } else {
                    // E-Mail mit den Accountdaten zusenden
                    $host = htmlentities($_SERVER['HTTP_HOST']);
                    $subject = str_replace(array('{title}', '{host}'), array(CONFIG_SEO_TITLE, $host), $this->lang->t('users', 'register_mail_subject'));
                    $body = str_replace(array('{name}', '{mail}', '{password}', '{title}', '{host}'), array($_POST['nickname'], $_POST['mail'], $_POST['pwd'], CONFIG_SEO_TITLE, $host), $this->lang->t('users', 'register_mail_message'));
                    $mail_sent = Core\Functions::generateEmail('', $_POST['mail'], $settings['mail'], $subject, $body);

                    $salt = Core\Functions::salt(12);
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
                        'registration_date' => $this->date->getCurrentDateTime(),
                    );

                    $this->db->beginTransaction();
                    try {
                        $bool = $this->db->insert(DB_PRE . 'users', $insert_values);
                        $user_id = $this->db->lastInsertId();
                        $bool2 = $this->db->insert(DB_PRE . 'acl_user_roles', array('user_id' => $user_id, 'role_id' => 2));
                        $this->db->commit();
                    } catch (\Exception $e) {
                        $this->db->rollback();
                        $bool = $bool2 = false;
                    }

                    $this->session->unsetFormToken();

                    $this->view->setContent(Core\Functions::confirmBox($this->lang->t('users', $mail_sent === true && $bool !== false && $bool2 !== false ? 'register_success' : 'register_error'), ROOT_DIR));
                }
            }
            if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
                $defaults = array(
                    'nickname' => '',
                    'mail' => '',
                );

                $this->view->assign('form', isset($_POST['submit']) ? $_POST : $defaults);

                if ($captchaAccess === true) {
                    $this->view->assign('captcha', \ACP3\Modules\Captcha\Helpers::captcha());
                }

                $this->session->generateFormToken();
            }
        }
    }

    public function actionViewProfile()
    {
        $this->breadcrumb
            ->append($this->lang->t('users', 'users'), $this->uri->route('users'))
            ->append($this->lang->t('users', 'view_profile'));

        if (Core\Validate::isNumber($this->uri->id) === true &&
            $this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'users WHERE id = ?', array($this->uri->id)) == 1
        ) {
            $user = $this->auth->getUserInfo($this->uri->id);
            $user['gender'] = str_replace(array(1, 2, 3), array('', $this->lang->t('users', 'female'), $this->lang->t('users', 'male')), $user['gender']);
            $user['birthday'] = $this->date->format($user['birthday'], $user['birthday_display'] == 1 ? 'd.m.Y' : 'd.m.');
            if (!empty($user['website']) && (bool)preg_match('=^http(s)?://=', $user['website']) === false)
                $user['website'] = 'http://' . $user['website'];

            $this->view->assign('user', $user);
        } else {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionSidebar()
    {
        $currentPage = base64_encode((defined('IN_ADM') === true ? 'acp/' : '') . $this->uri->query);

        // Usermenü anzeigen, falls der Benutzer eingeloggt ist
        if ($this->auth->isUser() === true) {
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
                        $nav_mods[$name]['active'] = defined('IN_ADM') === true && $dir === $this->uri->mod ? ' class="active"' : '';
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
                    $nav_system[$i]['name'] = $this->lang->t('system', 'acp_configuration');
                    $nav_system[$i]['active'] = $this->uri->query === 'system/configuration/' ? ' class="active"' : '';
                }
                if (Core\Modules::hasPermission('system', 'acp_extensions') === true) {
                    $i++;
                    $nav_system[$i]['page'] = 'extensions';
                    $nav_system[$i]['name'] = $this->lang->t('system', 'acp_extensions');
                    $nav_system[$i]['active'] = $this->uri->query === 'system/extensions/' ? ' class="active"' : '';
                }
                if (Core\Modules::hasPermission('system', 'acp_maintenance') === true) {
                    $i++;
                    $nav_system[$i]['page'] = 'maintenance';
                    $nav_system[$i]['name'] = $this->lang->t('system', 'acp_maintenance');
                    $nav_system[$i]['active'] = $this->uri->query === 'system/maintenance/' ? ' class="active"' : '';
                }
                $user_sidebar['system'] = $nav_system;
            }

            $this->view->assign('user_sidebar', $user_sidebar);

            $this->view->displayTemplate('users/sidebar_user_menu.tpl');
        } else {
            $settings = Core\Config::getSettings('users');

            $this->view->assign('enable_registration', $settings['enable_registration']);
            $this->view->assign('redirect_uri', isset($_POST['redirect_uri']) ? $_POST['redirect_uri'] : $currentPage);

            $this->view->displayTemplate('users/sidebar_login.tpl');
        }
    }

}