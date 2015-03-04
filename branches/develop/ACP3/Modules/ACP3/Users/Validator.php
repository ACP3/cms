<?php
namespace ACP3\Modules\ACP3\Users;

use ACP3\Core;

/**
 * Class Validator
 * @package ACP3\Modules\ACP3\Users
 */
class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @var Core\Validator\Rules\ACL
     */
    protected $aclValidator;
    /**
     * @var Core\Validator\Rules\Captcha
     */
    protected $captchaValidator;
    /**
     * @var Core\Validator\Rules\Date
     */
    protected $dateValidator;
    /**
     * @var \ACP3\Core\ACL
     */
    protected $acl;
    /**
     * @var \ACP3\Core\Auth
     */
    protected $auth;
    /**
     * @var Model
     */
    protected $userModel;

    /**
     * @param Core\Lang $lang
     * @param Core\Validator\Rules\Misc $validate
     * @param Core\Validator\Rules\ACL $aclValidator
     * @param Core\Validator\Rules\Captcha $captchaValidator
     * @param Core\Validator\Rules\Date $dateValidator
     * @param Core\ACL $acl
     * @param Core\Auth $auth
     * @param Model $userModel
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\ACL $aclValidator,
        Core\Validator\Rules\Captcha $captchaValidator,
        Core\Validator\Rules\Date $dateValidator,
        Core\ACL $acl,
        Core\Auth $auth,
        Model $userModel
    ) {
        parent::__construct($lang, $validate);

        $this->aclValidator = $aclValidator;
        $this->captchaValidator = $captchaValidator;
        $this->dateValidator = $dateValidator;
        $this->acl = $acl;
        $this->auth = $auth;
        $this->userModel = $userModel;
    }

    /**
     * @param array $formData
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateSettings(array $formData)
    {
        $this->validateFormKey();

        $this->errors = [];
        if (!empty($formData['mail']) && $this->validate->email($formData['mail']) === false) {
            $this->errors['mail'] = $this->lang->t('system', 'wrong_email_format');
        }
        if (!isset($formData['language_override']) || $formData['language_override'] != 1 && $formData['language_override'] != 0) {
            $this->errors['language-override'] = $this->lang->t('users', 'select_languages_override');
        }
        if (!isset($formData['entries_override']) || $formData['entries_override'] != 1 && $formData['entries_override'] != 0) {
            $this->errors['entries-override'] = $this->lang->t('users', 'select_entries_override');
        }
        if (!isset($formData['enable_registration']) || $formData['enable_registration'] != 1 && $formData['enable_registration'] != 0) {
            $this->errors['enable-registration'] = $this->lang->t('users', 'select_enable_registration');
        }

        $this->_checkForFailedValidation();
    }

    /**
     * @param array $formData
     * @param int $userId
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validate(array $formData, $userId = 0)
    {
        $this->validateFormKey();

        $this->errors = [];
        if (empty($formData['nickname'])) {
            $this->errors['nickname'] = $this->lang->t('system', 'name_to_short');
        }
        if ($this->_gender($formData['gender']) === false) {
            $this->errors['gender'] = $this->lang->t('users', 'select_gender');
        }
        if (!empty($formData['birthday']) && $this->dateValidator->birthday($formData['birthday']) === false) {
            $this->errors['date-birthday'] = $this->lang->t('users', 'invalid_birthday');
        }
        if ($this->userModel->resultExistsByUserName($formData['nickname'], $userId)) {
            $this->errors['nickname'] = $this->lang->t('users', 'user_name_already_exists');
        }
        if ($this->validate->email($formData['mail']) === false) {
            $this->errors['mail'] = $this->lang->t('system', 'wrong_email_format');
        }
        if ($this->userModel->resultExistsByEmail($formData['mail'], $userId)) {
            $this->errors['mail'] = $this->lang->t('users', 'user_email_already_exists');
        }
        if (empty($formData['roles']) || is_array($formData['roles']) === false || $this->aclValidator->aclRolesExist($formData['roles']) === false) {
            $this->errors['roles'] = $this->lang->t('users', 'select_access_level');
        }
        if (!isset($formData['super_user']) || ($formData['super_user'] != 1 && $formData['super_user'] != 0)) {
            $this->errors['super-user'] = $this->lang->t('users', 'select_super_user');
        }
        if ($this->lang->languagePackExists($formData['language']) === false) {
            $this->errors['language'] = $this->lang->t('users', 'select_language');
        }
        if ($this->validate->isNumber($formData['entries']) === false) {
            $this->errors['entries'] = $this->lang->t('system', 'select_records_per_page');
        }
        if (empty($formData['date_format_long'])) {
            $this->errors['date-format-long'] = $this->lang->t('system', 'type_in_long_date_format');
        }
        if (empty($formData['date_format_short'])) {
            $this->errors['date-format-short'] = $this->lang->t('system', 'type_in_short_date_format');
        }
        if ($this->dateValidator->timeZone($formData['date_time_zone']) === false) {
            $this->errors['time-zone'] = $this->lang->t('system', 'select_time_zone');
        }
        if (!empty($formData['icq']) && $this->_icq($formData['icq']) === false) {
            $this->errors['icq'] = $this->lang->t('users', 'invalid_icq_number');
        }
        if (in_array($formData['mail_display'], [0, 1]) === false) {
            $this->errors['mail-display'] = $this->lang->t('users', 'select_mail_display');
        }
        if (in_array($formData['address_display'], [0, 1]) === false) {
            $this->errors['address-display'] = $this->lang->t('users', 'select_address_display');
        }
        if (in_array($formData['country_display'], [0, 1]) === false) {
            $this->errors['country-display'] = $this->lang->t('users', 'select_country_display');
        }
        if (in_array($formData['birthday_display'], [0, 1, 2]) === false) {
            $this->errors['birthday-display'] = $this->lang->t('users', 'select_birthday_display');
        }
        if (isset($formData['new_pwd'])) {
            if (!empty($formData['new_pwd']) && !empty($formData['new_pwd_repeat']) && $formData['new_pwd'] != $formData['new_pwd_repeat']) {
                $this->errors['new-pwd'] = $this->lang->t('users', 'type_in_pwd');
            }
        } else {
            if (empty($_POST['pwd']) || empty($_POST['pwd_repeat']) || $_POST['pwd'] != $_POST['pwd_repeat']) {
                $this->errors['new-pwd'] = $this->lang->t('users', 'type_in_pwd');
            }
        }

        $this->_checkForFailedValidation();
    }

    /**
     * Bestimmung des Geschlechts
     *  1 = Keine Angabe
     *  2 = Weiblich
     *  3 = Männlich
     *
     * @param string , integer $var
     *               Die zu überprüfende Variable
     *
     * @return boolean
     */
    private function _gender($var)
    {
        return $var == 1 || $var == 2 || $var == 3;
    }

    /**
     * Überprüft, ob eine gültige ICQ-Nummer eingegeben wurde
     *
     * @param integer $var
     *
     * @return boolean
     */
    private function _icq($var)
    {
        return (bool)preg_match('/^(\d{6,9})$/', $var);
    }

    /**
     * @param array $formData
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateEditProfile(array $formData)
    {
        $this->validateFormKey();

        $this->errors = [];
        if (empty($formData['nickname'])) {
            $this->errors['nnickname'] = $this->lang->t('system', 'name_to_short');
        }
        if ($this->userModel->resultExistsByUserName($formData['nickname'], $this->auth->getUserId()) === true) {
            $this->errors['nickname'] = $this->lang->t('users', 'user_name_already_exists');
        }
        if ($this->_gender($formData['gender']) === false) {
            $this->errors['gender'] = $this->lang->t('users', 'select_gender');
        }
        if (!empty($formData['birthday']) && $this->dateValidator->birthday($formData['birthday']) === false) {
            $this->errors['date-birthday'] = $this->lang->t('users', 'invalid_birthday');
        }
        if ($this->validate->email($formData['mail']) === false) {
            $this->errors['mail'] = $this->lang->t('system', 'wrong_email_format');
        }
        if ($this->userModel->resultExistsByEmail($formData['mail'], $this->auth->getUserId()) === true) {
            $this->errors['mail'] = $this->lang->t('users', 'user_email_already_exists');
        }
        if (!empty($formData['icq']) && $this->_icq($formData['icq']) === false) {
            $this->errors['icq'] = $this->lang->t('users', 'invalid_icq_number');
        }
        if (!empty($formData['new_pwd']) && !empty($formData['new_pwd_repeat']) && $formData['new_pwd'] != $formData['new_pwd_repeat']) {
            $this->errors['new-pwd'] = $this->lang->t('users', 'type_in_pwd');
        }

        $this->_checkForFailedValidation();
    }

    /**
     * @param array $formData
     * @param array $settings
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateUserSettings(array $formData, array $settings)
    {
        $this->validateFormKey();

        $this->errors = [];
        if ($settings['language_override'] == 1 && $this->lang->languagePackExists($formData['language']) === false) {
            $this->errors['language'] = $this->lang->t('users', 'select_language');
        }
        if ($settings['entries_override'] == 1 && $this->validate->isNumber($formData['entries']) === false) {
            $this->errors['entries'] = $this->lang->t('system', 'select_records_per_page');
        }
        if (empty($formData['date_format_long'])) {
            $this->errors['date-format-long'] = $this->lang->t('system', 'type_in_long_date_format');
        }
        if (empty($formData['date_format_short'])) {
            $this->errors['date-format-short'] = $this->lang->t('system', 'type_in_short_date_format');
        }
        if ($this->dateValidator->timeZone($formData['date_time_zone']) === false) {
            $this->errors['time-zone'] = $this->lang->t('system', 'select_time_zone');
        }
        if (in_array($formData['mail_display'], [0, 1]) === false) {
            $this->errors['mail-display'] = $this->lang->t('users', 'select_mail_display');
        }
        if (in_array($formData['address_display'], [0, 1]) === false) {
            $this->errors['address-display'] = $this->lang->t('users', 'select_address_display');
        }
        if (in_array($formData['country_display'], [0, 1]) === false) {
            $this->errors['country-display'] = $this->lang->t('users', 'select_country_display');
        }
        if (in_array($formData['birthday_display'], [0, 1, 2]) === false) {
            $this->errors['birthday-display'] = $this->lang->t('users', 'select_birthday_display');
        }

        $this->_checkForFailedValidation();
    }

    /**
     * @param array $formData
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateForgotPassword(array $formData)
    {
        $this->validateFormKey();

        $this->errors = [];
        if (empty($formData['nick_mail'])) {
            $this->errors['nick-mail'] = $this->lang->t('users', 'type_in_nickname_or_email');
        } elseif ($this->validate->email($formData['nick_mail']) === false && $this->userModel->resultExistsByUserName($formData['nick_mail']) === false) {
            $this->errors['nick-mail'] = $this->lang->t('users', 'user_not_exists');
        } elseif ($this->validate->email($formData['nick_mail']) === true && $this->userModel->resultExistsByEmail($formData['nick_mail']) === false) {
            $this->errors['nick-mail'] = $this->lang->t('users', 'user_not_exists');
        }
        if ($this->acl->hasPermission('frontend/captcha/index/image') === true && $this->captchaValidator->captcha($formData['captcha']) === false) {
            $this->errors['captcha'] = $this->lang->t('captcha', 'invalid_captcha_entered');
        }

        $this->_checkForFailedValidation();
    }

    /**
     * @param array $formData
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateRegistration(array $formData)
    {
        $this->validateFormKey();

        $this->errors = [];
        if (empty($formData['nickname'])) {
            $this->errors['nickname'] = $this->lang->t('system', 'name_to_short');
        }
        if ($this->userModel->resultExistsByUserName($formData['nickname']) === true) {
            $this->errors['nickname'] = $this->lang->t('users', 'user_name_already_exists');
        }
        if ($this->validate->email($formData['mail']) === false) {
            $this->errors['mail'] = $this->lang->t('system', 'wrong_email_format');
        }
        if ($this->userModel->resultExistsByEmail($formData['mail']) === true) {
            $this->errors['mail'] = $this->lang->t('users', 'user_email_already_exists');
        }
        if (empty($formData['pwd']) || empty($formData['pwd_repeat']) || $formData['pwd'] != $formData['pwd_repeat']) {
            $this->errors['pwd'] = $this->lang->t('users', 'type_in_pwd');
        }
        if ($this->acl->hasPermission('frontend/captcha/index/image') === true && $this->captchaValidator->captcha($formData['captcha']) === false) {
            $this->errors['captcha'] = $this->lang->t('captcha', 'invalid_captcha_entered');
        }

        $this->_checkForFailedValidation();
    }
}
