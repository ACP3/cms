<?php

namespace ACP3\Modules\Users\Controller;

use ACP3\Core;
use ACP3\Modules\Users;

/**
 * Class Account
 * @package ACP3\Modules\Users\Controller
 */
class Account extends Core\Modules\Controller\Frontend
{
    /**
     * @var Core\Date
     */
    protected $date;
    /**
     * @var Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var Users\Model
     */
    protected $usersModel;
    /**
     * @var Core\Config
     */
    protected $usersConfig;

    public function __construct(
        Core\Context\Frontend $context,
        Core\Date $date,
        Core\Helpers\Secure $secureHelper,
        Users\Model $usersModel,
        Core\Config $usersConfig)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->secureHelper = $secureHelper;
        $this->usersModel = $usersModel;
        $this->usersConfig = $usersConfig;
    }

    public function preDispatch()
    {
        parent::preDispatch();

        if ($this->auth->isUser() === false || $this->get('core.validator.rules.misc')->isNumber($this->auth->getUserId()) === false) {
            $this->redirect()->temporary('users/index/login');
        }
    }

    public function actionEdit()
    {
        if (empty($_POST) === false) {
            try {
                $validator = $this->get('users.validator');
                $validator->validateEditProfile($_POST);

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
                    $securityHelper = $this->get('core.helpers.secure');

                    $salt = $securityHelper->salt(12);
                    $newPassword = $securityHelper->generateSaltedPassword($salt, $_POST['new_pwd']);
                    $updateValues['pwd'] = $newPassword . ':' . $salt;
                }

                $bool = $this->usersModel->update($updateValues, $this->auth->getUserId());

                $cookieArr = explode('|', base64_decode($_COOKIE['ACP3_AUTH']));
                $this->auth->setCookie($_POST['nickname'], isset($newPassword) ? $newPassword : $cookieArr[1], 3600);

                $this->secureHelper->unsetFormToken($this->request->query);

                $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'users/account');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                $this->redirectMessages()->setMessage(false, $e->getMessage(), 'users/account');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
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

        $this->secureHelper->generateFormToken($this->request->query);
    }

    public function actionSettings()
    {
        $settings = $this->usersConfig->getSettings();

        if (empty($_POST) === false) {
            try {
                $validator = $this->get('users.validator');
                $validator->validateUserSettings($_POST, $settings);

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

                $bool = $this->usersModel->update($updateValues, $this->auth->getUserId());

                $this->secureHelper->unsetFormToken($this->request->query);

                $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'settings_success' : 'settings_error'), 'users/account');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                $this->redirectMessages()->setMessage(false, $e->getMessage(), 'users/account');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
            }
        }

        $user = $this->usersModel->getOneById($this->auth->getUserId());

        $this->view->assign('language_override', $settings['language_override']);
        $this->view->assign('entries_override', $settings['entries_override']);

        // Sprache
        $this->view->assign('languages', $this->lang->getLanguages(isset($_POST['language']) ? $_POST['language'] : $user['language']));

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

        $this->secureHelper->generateFormToken($this->request->query);
    }

    public function actionIndex()
    {
        if (empty($_POST) === false) {
            $updateValues = array(
                'draft' => Core\Functions::strEncode($_POST['draft'], true)
            );
            $bool = $this->usersModel->update($updateValues, $this->auth->getUserId());

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'users/account');
        }

        $this->redirectMessages()->getMessage();

        $user = $this->usersModel->getOneById($this->auth->getUserId());

        $this->view->assign('draft', $user['draft']);
    }

}