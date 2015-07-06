<?php

namespace ACP3\Modules\ACP3\Users\Controller;

use ACP3\Core;
use ACP3\Modules\ACP3\Users;

/**
 * Class Account
 * @package ACP3\Modules\ACP3\Users\Controller
 */
class Account extends Core\Modules\Controller\Frontend
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model
     */
    protected $usersModel;
    /**
     * @var \ACP3\Modules\ACP3\Users\Validator
     */
    protected $usersValidator;

    /**
     * @param \ACP3\Core\Context\Frontend   $context
     * @param \ACP3\Core\Date               $date
     * @param \ACP3\Core\Helpers\Secure     $secureHelper
     * @param \ACP3\Modules\ACP3\Users\Model     $usersModel
     * @param \ACP3\Modules\ACP3\Users\Validator $usersValidator
     */
    public function __construct(
        Core\Context\Frontend $context,
        Core\Date $date,
        Core\Helpers\Secure $secureHelper,
        Users\Model $usersModel,
        Users\Validator $usersValidator)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->secureHelper = $secureHelper;
        $this->usersModel = $usersModel;
        $this->usersValidator = $usersValidator;
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
            $this->_editPost($_POST);
        }

        $user = $this->auth->getUserInfo();

        // Geschlecht
        $lang_gender = [
            $this->lang->t('users', 'gender_not_specified'),
            $this->lang->t('users', 'gender_female'),
            $this->lang->t('users', 'gender_male')
        ];
        $this->view->assign('gender', $this->get('core.helpers.forms')->selectGenerator('gender', [1, 2, 3], $lang_gender, $user['gender']));

        // Geburtstag
        $datepickerParams = ['constrainInput' => 'true', 'changeMonth' => 'true', 'changeYear' => 'true', 'yearRange' => '\'-50:+0\''];
        $this->view->assign('birthday_datepicker', $this->date->datepicker('birthday', $user['birthday'], 'Y-m-d', $datepickerParams, 0, false, true));

        // Kontaktangaben
        $contact = [];
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
        $countries_select = [];
        foreach ($countries as $key => $value) {
            $countries_select[] = [
                'value' => $key,
                'lang' => $value,
                'selected' => $this->get('core.helpers.forms')->selectEntry('countries', $key, $user['country']),
            ];
        }
        $this->view->assign('countries', $countries_select);

        $this->view->assign('form', array_merge($user, $_POST));

        $this->secureHelper->generateFormToken($this->request->getQuery());
    }

    public function actionSettings()
    {
        $settings = $this->config->getSettings('users');

        if (empty($_POST) === false) {
            $this->_settingsPost($_POST, $settings);
        }

        $user = $this->usersModel->getOneById($this->auth->getUserId());

        $this->view->assign('language_override', $settings['language_override']);
        $this->view->assign('entries_override', $settings['entries_override']);

        // Sprache
        $this->view->assign('languages', $this->lang->getLanguagePack(isset($_POST['language']) ? $_POST['language'] : $user['language']));

        // Einträge pro Seite
        $this->view->assign('entries', $this->get('core.helpers.forms')->recordsPerPage((int)$user['entries']));

        // Zeitzonen
        $this->view->assign('time_zones', $this->date->getTimeZones($user['time_zone']));

        $lang_mailDisplay = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
        $this->view->assign('mail_display', $this->get('core.helpers.forms')->selectGenerator('mail_display', [1, 0], $lang_mailDisplay, $user['mail_display'], 'checked'));

        $lang_addressDisplay = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
        $this->view->assign('address_display', $this->get('core.helpers.forms')->selectGenerator('address_display', [1, 0], $lang_addressDisplay, $user['address_display'], 'checked'));

        $lang_countryDisplay = [$this->lang->t('system', 'yes'), $this->lang->t('system', 'no')];
        $this->view->assign('country_display', $this->get('core.helpers.forms')->selectGenerator('country_display', [1, 0], $lang_countryDisplay, $user['country_display'], 'checked'));

        $lang_birthdayDisplay = [
            $this->lang->t('users', 'birthday_hide'),
            $this->lang->t('users', 'birthday_display_completely'),
            $this->lang->t('users', 'birthday_hide_year')
        ];
        $this->view->assign('birthday_display', $this->get('core.helpers.forms')->selectGenerator('birthday_display', [0, 1, 2], $lang_birthdayDisplay, $user['birthday_display'], 'checked'));

        $this->view->assign('form', array_merge($user, $_POST));

        $this->secureHelper->generateFormToken($this->request->getQuery());
    }

    public function actionIndex()
    {
        if (empty($_POST) === false) {
            $updateValues = [
                'draft' => Core\Functions::strEncode($_POST['draft'], true)
            ];
            $bool = $this->usersModel->update($updateValues, $this->auth->getUserId());

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'));
        }

        $user = $this->usersModel->getOneById($this->auth->getUserId());

        $this->view->assign('draft', $user['draft']);
    }

    protected function _editPost(array $formData)
    {
        try {
            $this->usersValidator->validateEditProfile($formData);

            $updateValues = [
                'nickname' => Core\Functions::strEncode($formData['nickname']),
                'realname' => Core\Functions::strEncode($formData['realname']),
                'gender' => (int)$formData['gender'],
                'birthday' => $formData['birthday'],
                'mail' => $formData['mail'],
                'website' => Core\Functions::strEncode($formData['website']),
                'icq' => $formData['icq'],
                'skype' => Core\Functions::strEncode($formData['skype']),
                'street' => Core\Functions::strEncode($formData['street']),
                'house_number' => Core\Functions::strEncode($formData['house_number']),
                'zip' => Core\Functions::strEncode($formData['zip']),
                'city' => Core\Functions::strEncode($formData['city']),
                'country' => Core\Functions::strEncode($formData['country']),
            ];

            // Neues Passwort
            if (!empty($formData['new_pwd']) && !empty($formData['new_pwd_repeat'])) {
                $securityHelper = $this->get('core.helpers.secure');

                $salt = $securityHelper->salt(12);
                $newPassword = $securityHelper->generateSaltedPassword($salt, $formData['new_pwd']);
                $updateValues['pwd'] = $newPassword . ':' . $salt;
            }

            $bool = $this->usersModel->update($updateValues, $this->auth->getUserId());

            $cookieArr = explode('|', base64_decode($_COOKIE['ACP3_AUTH']));
            $this->auth->setCookie($formData['nickname'], isset($newPassword) ? $newPassword : $cookieArr[1], 3600);

            $this->secureHelper->unsetFormToken($this->request->getQuery());

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    protected function _settingsPost(array $formData, array $settings)
    {
        try {
            $this->usersValidator->validateUserSettings($formData, $settings);

            $updateValues = [
                'mail_display' => (int)$formData['mail_display'],
                'birthday_display' => (int)$formData['birthday_display'],
                'address_display' => (int)$formData['address_display'],
                'country_display' => (int)$formData['country_display'],
                'date_format_long' => Core\Functions::strEncode($formData['date_format_long']),
                'date_format_short' => Core\Functions::strEncode($formData['date_format_short']),
                'time_zone' => $formData['date_time_zone'],
            ];
            if ($settings['language_override'] == 1) {
                $updateValues['language'] = $formData['language'];
            }
            if ($settings['entries_override'] == 1) {
                $updateValues['entries'] = (int)$formData['entries'];
            }

            $bool = $this->usersModel->update($updateValues, $this->auth->getUserId());

            $this->secureHelper->unsetFormToken($this->request->getQuery());

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'settings_success' : 'settings_error'));
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage());
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }
}
