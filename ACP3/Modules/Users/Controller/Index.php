<?php

namespace ACP3\Modules\Users\Controller;

use ACP3\Core;
use ACP3\Modules\Users;

/**
 * Description of UsersFrontend
 *
 * @author Tino Goratsch
 */
class Index extends Core\Modules\Controller
{
    /**
     * @var Users\Model
     */
    protected $model;

    protected function _init()
    {
        $this->model = new Users\Model($this->db, $this->lang, $this->auth, $this->uri);
    }

    public function actionEditProfile()
    {
        if ($this->auth->isUser() === false || Core\Validate::isNumber($this->auth->getUserId()) === false) {
            $this->uri->redirect('errors/index/403');
        } else {
            $this->breadcrumb
                ->append($this->lang->t('users', 'users'), $this->uri->route('users'))
                ->append($this->lang->t('users', 'home'), $this->uri->route('users/index/home'))
                ->append($this->lang->t('users', 'edit_profile'));

            if (empty($_POST) === false) {
                try {
                    $this->model->validateEditProfile($_POST);

                    $updateValues = array(
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
                        $newPassword = Core\Functions::generateSaltedPassword($salt, $_POST['new_pwd']);
                        $updateValues['pwd'] = $newPassword . ':' . $salt;
                    }

                    $bool = $this->model->update($updateValues, $this->auth->getUserId());

                    $cookieArr = explode('|', base64_decode($_COOKIE['ACP3_AUTH']));
                    $this->auth->setCookie($_POST['nickname'], isset($newPassword) ? $newPassword : $cookieArr[1], 3600);

                    $this->session->unsetFormToken();

                    Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'users/home');
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    Core\Functions::setRedirectMessage(false, $e->getMessage(), 'users/home');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $this->view->assign('error_msg', $e->getMessage());
                }
            }

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
            $contact[0]['value'] = empty($_POST) === false ? $_POST['mail'] : $user['mail'];
            $contact[0]['maxlength'] = '120';
            $contact[1]['name'] = 'website';
            $contact[1]['lang'] = $this->lang->t('system', 'website');
            $contact[1]['value'] = empty($_POST) === false ? $_POST['website'] : $user['website'];
            $contact[1]['maxlength'] = '120';
            $contact[2]['name'] = 'icq';
            $contact[2]['lang'] = $this->lang->t('users', 'icq');
            $contact[2]['value'] = empty($_POST) === false ? $_POST['icq'] : $user['icq'];
            $contact[2]['maxlength'] = '9';
            $contact[3]['name'] = 'skype';
            $contact[3]['lang'] = $this->lang->t('users', 'skype');
            $contact[3]['value'] = empty($_POST) === false ? $_POST['skype'] : $user['skype'];
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

            $this->view->assign('form', array_merge($user, $_POST));

            $this->session->generateFormToken();
        }
    }

    public function actionEditSettings()
    {
        if ($this->auth->isUser() === false || Core\Validate::isNumber($this->auth->getUserId()) === false) {
            $this->uri->redirect('errors/index/403');
        } else {
            $settings = Core\Config::getSettings('users');

            $this->breadcrumb
                ->append($this->lang->t('users', 'users'), $this->uri->route('users'))
                ->append($this->lang->t('users', 'home'), $this->uri->route('users/index/home'))
                ->append($this->lang->t('users', 'edit_settings'));

            if (empty($_POST) === false) {
                try {
                    $this->model->validateUserSettings($_POST, $settings);

                    $updateValues = array(
                        'mail_display' => (int)$_POST['mail_display'],
                        'birthday_display' => (int)$_POST['birthday_display'],
                        'address_display' => (int)$_POST['address_display'],
                        'country_display' => (int)$_POST['country_display'],
                        'date_format_long' => Core\Functions::strEncode($_POST['date_format_long']),
                        'date_format_short' => Core\Functions::strEncode($_POST['date_format_short']),
                        'time_zone' => $_POST['date_time_zone'],
                    );
                    if ($settings['language_override'] == 1) {
                        $updateValues['language'] = $_POST['language'];
                    }
                    if ($settings['entries_override'] == 1) {
                        $updateValues['entries'] = (int)$_POST['entries'];
                    }

                    $bool = $this->model->update($updateValues, $this->auth->getUserId());

                    $this->session->unsetFormToken();

                    Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'settings_success' : 'settings_error'), 'users/home');
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    Core\Functions::setRedirectMessage(false, $e->getMessage(), 'users/home');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $this->view->assign('error_msg', $e->getMessage());
                }
            }

            $user = $this->model->getOneById($this->auth->getUserId());

            $this->view->assign('language_override', $settings['language_override']);
            $this->view->assign('entries_override', $settings['entries_override']);

            // Sprache
            $languages = array();
            $langDir = scandir(ACP3_ROOT_DIR . 'languages');
            $c_langDir = count($langDir);
            for ($i = 0; $i < $c_langDir; ++$i) {
                $langInfo = Core\XML::parseXmlFile(ACP3_ROOT_DIR . 'languages/' . $langDir[$i] . '/info.xml', '/language');
                if (!empty($langInfo)) {
                    $name = $langInfo['name'];
                    $languages[$name]['dir'] = $langDir[$i];
                    $languages[$name]['selected'] = Core\Functions::selectEntry('language', $langDir[$i], $user['language']);
                    $languages[$name]['name'] = $langInfo['name'];
                }
            }
            ksort($languages);
            $this->view->assign('languages', $languages);

            // Einträge pro Seite
            $this->view->assign('entries', Core\Functions::recordsPerPage((int)$user['entries']));

            // Zeitzonen
            $this->view->assign('time_zones', Core\Date::getTimeZones($user['time_zone']));

            $lang_mailDisplay = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
            $this->view->assign('mail_display', Core\Functions::selectGenerator('mail_display', array(1, 0), $lang_mailDisplay, $user['mail_display'], 'checked'));

            $lang_addressDisplay = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
            $this->view->assign('address_display', Core\Functions::selectGenerator('address_display', array(1, 0), $lang_addressDisplay, $user['address_display'], 'checked'));

            $lang_countryDisplay = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
            $this->view->assign('country_display', Core\Functions::selectGenerator('country_display', array(1, 0), $lang_countryDisplay, $user['country_display'], 'checked'));

            $lang_birthdayDisplay = array(
                $this->lang->t('users', 'birthday_hide'),
                $this->lang->t('users', 'birthday_display_completely'),
                $this->lang->t('users', 'birthday_hide_year')
            );
            $this->view->assign('birthday_display', Core\Functions::selectGenerator('birthday_display', array(0, 1, 2), $lang_birthdayDisplay, $user['birthday_display'], 'checked'));

            $this->view->assign('form', array_merge($user, $_POST));

            $this->session->generateFormToken();
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

            if (empty($_POST) === false) {
                try {
                    $this->model->validateForgotPassword($_POST);

                    // Neues Passwort und neuen Zufallsschlüssel erstellen
                    $newPassword = Core\Functions::salt(8);
                    $host = htmlentities($_SERVER['HTTP_HOST']);

                    // Je nachdem, wie das Feld ausgefüllt wurde, dieses auswählen
                    if (Core\Validate::email($_POST['nick_mail']) === true && $this->model->resultExistsByEmail($_POST['nick_mail']) === true) {
                        $user = $this->model->getOneByEmail($_POST['nick_mail']);
                    } else {
                        $user = $this->model->getOneByNickname($_POST['nick_mail']);
                    }

                    // E-Mail mit dem neuen Passwort versenden
                    $subject = str_replace(array('{title}', '{host}'), array(CONFIG_SEO_TITLE, $host), $this->lang->t('users', 'forgot_pwd_mail_subject'));
                    $search = array('{name}', '{mail}', '{password}', '{title}', '{host}');
                    $replace = array($user['nickname'], $user['mail'], $newPassword, CONFIG_SEO_TITLE, $host);
                    $body = str_replace($search, $replace, $this->lang->t('users', 'forgot_pwd_mail_message'));

                    $settings = Core\Config::getSettings('users');
                    $mailIsSent = Core\Functions::generateEmail(substr($user['realname'], 0, -2), $user['mail'], $settings['mail'], $subject, $body);

                    // Das Passwort des Benutzers nur abändern, wenn die E-Mail erfolgreich versendet werden konnte
                    if ($mailIsSent === true) {
                        $salt = Core\Functions::salt(12);
                        $updateValues = array(
                            'pwd' => Core\Functions::generateSaltedPassword($salt, $newPassword) . ':' . $salt,
                            'login_errors' => 0
                        );
                        $bool = $this->model->update($updateValues, $user['id']);
                    }

                    $this->session->unsetFormToken();

                    $this->view->setContent(Core\Functions::confirmBox($this->lang->t('users', $mailIsSent === true && isset($bool) && $bool !== false ? 'forgot_pwd_success' : 'forgot_pwd_error'), ROOT_DIR));
                    return;
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    Core\Functions::setRedirectMessage(false, $e->getMessage(), 'users/forgot_pwd');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $this->view->assign('error_msg', $e->getMessage());
                }
            }

            $this->view->assign('form', array_merge(array('nick_mail' => ''), $_POST));

            if (Core\Modules::hasPermission('frontend/captcha/index/image') === true) {
                $this->view->assign('captcha', \ACP3\Modules\Captcha\Helpers::captcha());
            }

            $this->session->generateFormToken();
        }
    }

    public function actionHome()
    {
        if ($this->auth->isUser() === false || !Core\Validate::isNumber($this->auth->getUserId())) {
            $this->uri->redirect('errors/index/403');
        } else {
            $this->breadcrumb
                ->append($this->lang->t('users', 'users'), $this->uri->route('users'))
                ->append($this->lang->t('users', 'home'));

            if (empty($_POST) === false) {
                $updateValues = array(
                    'draft' => Core\Functions::strEncode($_POST['draft'], true)
                );
                $bool = $this->model->update($updateValues, $this->auth->getUserId());

                Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'users/home');
            }

            Core\Functions::getRedirectMessage();

            $user = $this->model->getOneById($this->auth->getUserId());

            $this->view->assign('draft', $user['draft']);
        }
    }

    public function actionIndex()
    {
        $users = $this->model->getAll(POS, $this->auth->entries);
        $c_users = count($users);
        $allUsers = $this->model->countAll();

        if ($c_users > 0) {
            $pagination = new Core\Pagination(
                $this->auth,
                $this->breadcrumb,
                $this->lang,
                $this->seo,
                $this->uri,
                $this->view,
                $allUsers
            );
            $pagination->display();

            for ($i = 0; $i < $c_users; ++$i) {
                if (!empty($users[$i]['website']) && (bool)preg_match('=^http(s)?://=', $users[$i]['website']) === false) {
                    $users[$i]['website'] = 'http://' . $users[$i]['website'];
                }
            }
            $this->view->assign('users', $users);
        }
        $this->view->assign('LANG_users_found', sprintf($this->lang->t('users', 'users_found'), $allUsers));
    }

    public function actionLogin()
    {
        // Falls der Benutzer schon eingeloggt ist, diesen zur Startseite weiterleiten
        if ($this->auth->isUser() === true) {
            $this->uri->redirect(0, ROOT_DIR);
        } elseif (empty($_POST) === false) {
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

            if (empty($_POST) === false) {
                try {
                    $this->model->validateRegistration($_POST);

                    // E-Mail mit den Accountdaten zusenden
                    $host = htmlentities($_SERVER['HTTP_HOST']);
                    $subject = str_replace(array('{title}', '{host}'), array(CONFIG_SEO_TITLE, $host), $this->lang->t('users', 'register_mail_subject'));
                    $body = str_replace(array('{name}', '{mail}', '{password}', '{title}', '{host}'), array($_POST['nickname'], $_POST['mail'], $_POST['pwd'], CONFIG_SEO_TITLE, $host), $this->lang->t('users', 'register_mail_message'));
                    $mailIsSent = Core\Functions::generateEmail('', $_POST['mail'], $settings['mail'], $subject, $body);

                    $salt = Core\Functions::salt(12);
                    $insertValues = array(
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
                        $lastId = $this->model->insert($insertValues);
                        $bool2 = $this->db->insert(DB_PRE . 'acl_user_roles', array('user_id' => $lastId, 'role_id' => 2));
                        $this->db->commit();
                    } catch (\Exception $e) {
                        $this->db->rollback();
                        $lastId = $bool2 = false;
                    }

                    $this->session->unsetFormToken();

                    $this->view->setContent(Core\Functions::confirmBox($this->lang->t('users', $mailIsSent === true && $lastId !== false && $bool2 !== false ? 'register_success' : 'register_error'), ROOT_DIR));
                    return;
                } catch (Core\Exceptions\InvalidFormToken $e) {
                    Core\Functions::setRedirectMessage(false, $e->getMessage(), 'users/register');
                } catch (Core\Exceptions\ValidationFailed $e) {
                    $this->view->assign('error_msg', $e->getMessage());
                }
            }

            $defaults = array(
                'nickname' => '',
                'mail' => '',
            );

            $this->view->assign('form', array_merge($defaults, $_POST));

            if (Core\Modules::hasPermission('frontend/captcha/index/image') === true) {
                $this->view->assign('captcha', \ACP3\Modules\Captcha\Helpers::captcha());
            }

            $this->session->generateFormToken();
        }
    }

    public function actionViewProfile()
    {
        $this->breadcrumb
            ->append($this->lang->t('users', 'users'), $this->uri->route('users'))
            ->append($this->lang->t('users', 'view_profile'));

        if (Core\Validate::isNumber($this->uri->id) === true && $this->model->resultExists($this->uri->id) === true) {
            $user = $this->auth->getUserInfo($this->uri->id);
            $user['gender'] = str_replace(array(1, 2, 3), array('', $this->lang->t('users', 'female'), $this->lang->t('users', 'male')), $user['gender']);
            $user['birthday'] = $this->date->format($user['birthday'], $user['birthday_display'] == 1 ? 'd.m.Y' : 'd.m.');
            if (!empty($user['website']) && (bool)preg_match('=^http(s)?://=', $user['website']) === false) {
                $user['website'] = 'http://' . $user['website'];
            }

            $this->view->assign('user', $user);
        } else {
            $this->uri->redirect('errors/index/404');
        }
    }

}