<?php
namespace ACP3\Modules\Users;

use ACP3\Core;

/**
 * Class Validator
 * @package ACP3\Modules\Users
 */
class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @var \ACP3\Core\Auth
     */
    protected $auth;
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Core\Request
     */
    protected $uri;
    /**
     * @var Model
     */
    protected $userModel;

    public function __construct(Core\Lang $lang, Core\Validate $validate, Core\Auth $auth, Core\Modules $modules, Core\Request $uri, Model $userModel)
    {
        parent::__construct($lang, $validate);

        $this->auth = $auth;
        $this->modules = $modules;
        $this->uri = $uri;
        $this->userModel = $userModel;
    }

    /**
     * @param array $formData
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateSettings(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (!empty($formData['mail']) && $this->validate->email($formData['mail']) === false) {
            $errors['mail'] = $this->lang->t('system', 'wrong_email_format');
        }
        if (!isset($formData['language_override']) || $formData['language_override'] != 1 && $formData['language_override'] != 0) {
            $errors[] = $this->lang->t('users', 'select_languages_override');
        }
        if (!isset($formData['entries_override']) || $formData['entries_override'] != 1 && $formData['entries_override'] != 0) {
            $errors[] = $this->lang->t('users', 'select_entries_override');
        }
        if (!isset($formData['enable_registration']) || $formData['enable_registration'] != 1 && $formData['enable_registration'] != 0) {
            $errors[] = $this->lang->t('users', 'select_enable_registration');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

    /**
     * @param array $formData
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateCreate(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (empty($formData['nickname'])) {
            $errors['nickname'] = $this->lang->t('system', 'name_to_short');
        }
        if ($this->validate->gender($formData['gender']) === false) {
            $errors['gender'] = $this->lang->t('users', 'select_gender');
        }
        if (!empty($formData['birthday']) && $this->validate->birthday($formData['birthday']) === false) {
            $errors[] = $this->lang->t('users', 'invalid_birthday');
        }
        if ($this->userModel->resultExistsByUserName($formData['nickname'])) {
            $errors['nickname'] = $this->lang->t('users', 'user_name_already_exists');
        }
        if ($this->validate->email($formData['mail']) === false) {
            $errors['mail'] = $this->lang->t('system', 'wrong_email_format');
        }
        if ($this->userModel->resultExistsByEmail($formData['mail'])) {
            $errors['mail'] = $this->lang->t('users', 'user_email_already_exists');
        }
        if (empty($formData['roles']) || is_array($formData['roles']) === false || $this->validate->aclRolesExist($formData['roles']) === false) {
            $errors['roles'] = $this->lang->t('users', 'select_access_level');
        }
        if (!isset($formData['super_user']) || ($formData['super_user'] != 1 && $formData['super_user'] != 0)) {
            $errors['super-user'] = $this->lang->t('users', 'select_super_user');
        }
        if ($this->lang->languagePackExists($formData['language']) === false) {
            $errors['language'] = $this->lang->t('users', 'select_language');
        }
        if ($this->validate->isNumber($formData['entries']) === false) {
            $errors['entries'] = $this->lang->t('system', 'select_records_per_page');
        }
        if (empty($formData['date_format_long']) || empty($formData['date_format_short'])) {
            $errors[] = $this->lang->t('system', 'type_in_date_format');
        }
        if ($this->validate->timeZone($formData['date_time_zone']) === false) {
            $errors['time-zone'] = $this->lang->t('system', 'select_time_zone');
        }
        if (!empty($formData['icq']) && $this->validate->icq($formData['icq']) === false) {
            $errors['icq'] = $this->lang->t('users', 'invalid_icq_number');
        }
        if (in_array($formData['mail_display'], array(0, 1)) === false) {
            $errors[] = $this->lang->t('users', 'select_mail_display');
        }
        if (in_array($formData['address_display'], array(0, 1)) === false) {
            $errors[] = $this->lang->t('users', 'select_address_display');
        }
        if (in_array($formData['country_display'], array(0, 1)) === false) {
            $errors[] = $this->lang->t('users', 'select_country_display');
        }
        if (in_array($formData['birthday_display'], array(0, 1, 2)) === false) {
            $errors[] = $this->lang->t('users', 'select_birthday_display');
        }
        if (empty($_POST['pwd']) || empty($_POST['pwd_repeat']) || $_POST['pwd'] != $_POST['pwd_repeat']) {
            $errors[] = $this->lang->t('users', 'type_in_pwd');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

    /**
     * @param array $formData
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateEdit(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (empty($formData['nickname'])) {
            $errors['nickname'] = $this->lang->t('system', 'name_to_short');
        }
        if ($this->validate->gender($formData['gender']) === false) {
            $errors['gender'] = $this->lang->t('users', 'select_gender');
        }
        if (!empty($formData['birthday']) && $this->validate->birthday($formData['birthday']) === false) {
            $errors[] = $this->lang->t('users', 'invalid_birthday');
        }
        if ($this->userModel->resultExistsByUserName($formData['nickname'], $this->uri->id)) {
            $errors['nickname'] = $this->lang->t('users', 'user_name_already_exists');
        }
        if ($this->validate->email($formData['mail']) === false) {
            $errors['mail'] = $this->lang->t('system', 'wrong_email_format');
        }
        if ($this->userModel->resultExistsByEmail($formData['mail'], $this->uri->id)) {
            $errors['mail'] = $this->lang->t('users', 'user_email_already_exists');
        }
        if (empty($formData['roles']) || is_array($formData['roles']) === false || $this->validate->aclRolesExist($formData['roles']) === false) {
            $errors['roles'] = $this->lang->t('users', 'select_access_level');
        }
        if (!isset($formData['super_user']) || ($formData['super_user'] != 1 && $formData['super_user'] != 0)) {
            $errors['super-user'] = $this->lang->t('users', 'select_super_user');
        }
        if ($this->lang->languagePackExists($formData['language']) === false) {
            $errors['language'] = $this->lang->t('users', 'select_language');
        }
        if ($this->validate->isNumber($formData['entries']) === false) {
            $errors['entries'] = $this->lang->t('system', 'select_records_per_page');
        }
        if (empty($formData['date_format_long']) || empty($formData['date_format_short'])) {
            $errors[] = $this->lang->t('system', 'type_in_date_format');
        }
        if ($this->validate->timeZone($formData['date_time_zone']) === false) {
            $errors['time-zone'] = $this->lang->t('system', 'select_time_zone');
        }
        if (!empty($formData['icq']) && $this->validate->icq($formData['icq']) === false) {
            $errors['icq'] = $this->lang->t('users', 'invalid_icq_number');
        }
        if (in_array($formData['mail_display'], array(0, 1)) === false) {
            $errors[] = $this->lang->t('users', 'select_mail_display');
        }
        if (in_array($formData['address_display'], array(0, 1)) === false) {
            $errors[] = $this->lang->t('users', 'select_address_display');
        }
        if (in_array($formData['country_display'], array(0, 1)) === false) {
            $errors[] = $this->lang->t('users', 'select_country_display');
        }
        if (in_array($formData['birthday_display'], array(0, 1, 2)) === false) {
            $errors[] = $this->lang->t('users', 'select_birthday_display');
        }
        if (!empty($formData['new_pwd']) && !empty($formData['new_pwd_repeat']) && $formData['new_pwd'] != $formData['new_pwd_repeat']) {
            $errors[] = $this->lang->t('users', 'type_in_pwd');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

    /**
     * @param array $formData
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateEditProfile(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (empty($formData['nickname'])) {
            $errors['nnickname'] = $this->lang->t('system', 'name_to_short');
        }
        if ($this->userModel->resultExistsByUserName($formData['nickname'], $this->auth->getUserId()) === true) {
            $errors['nickname'] = $this->lang->t('users', 'user_name_already_exists');
        }
        if ($this->validate->gender($formData['gender']) === false) {
            $errors['gender'] = $this->lang->t('users', 'select_gender');
        }
        if (!empty($formData['birthday']) && $this->validate->birthday($formData['birthday']) === false) {
            $errors[] = $this->lang->t('users', 'invalid_birthday');
        }
        if ($this->validate->email($formData['mail']) === false) {
            $errors['mail'] = $this->lang->t('system', 'wrong_email_format');
        }
        if ($this->userModel->resultExistsByEmail($formData['mail'], $this->auth->getUserId()) === true) {
            $errors['mail'] = $this->lang->t('users', 'user_email_already_exists');
        }
        if (!empty($formData['icq']) && $this->validate->icq($formData['icq']) === false) {
            $errors['icq'] = $this->lang->t('users', 'invalid_icq_number');
        }
        if (!empty($formData['new_pwd']) && !empty($formData['new_pwd_repeat']) && $formData['new_pwd'] != $formData['new_pwd_repeat']) {
            $errors[] = $this->lang->t('users', 'type_in_pwd');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

    /**
     * @param array $formData
     * @param array $settings
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateUserSettings(array $formData, array $settings)
    {
        $this->validateFormKey();

        $errors = array();
        if ($settings['language_override'] == 1 && $this->lang->languagePackExists($formData['language']) === false) {
            $errors['language'] = $this->lang->t('users', 'select_language');
        }
        if ($settings['entries_override'] == 1 && $this->validate->isNumber($formData['entries']) === false) {
            $errors['entries'] = $this->lang->t('system', 'select_records_per_page');
        }
        if (empty($formData['date_format_long']) || empty($formData['date_format_short'])) {
            $errors[] = $this->lang->t('system', 'type_in_date_format');
        }
        if ($this->validate->timeZone($formData['date_time_zone']) === false) {
            $errors['time-zone'] = $this->lang->t('system', 'select_time_zone');
        }
        if (in_array($formData['mail_display'], array(0, 1)) === false) {
            $errors[] = $this->lang->t('users', 'select_mail_display');
        }
        if (in_array($formData['address_display'], array(0, 1)) === false) {
            $errors[] = $this->lang->t('users', 'select_address_display');
        }
        if (in_array($formData['country_display'], array(0, 1)) === false) {
            $errors[] = $this->lang->t('users', 'select_country_display');
        }
        if (in_array($formData['birthday_display'], array(0, 1, 2)) === false) {
            $errors[] = $this->lang->t('users', 'select_birthday_display');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

    /**
     * @param array $formData
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateForgotPassword(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (empty($formData['nick_mail'])) {
            $errors['nick-mail'] = $this->lang->t('users', 'type_in_nickname_or_email');
        } elseif ($this->validate->email($formData['nick_mail']) === false && $this->userModel->resultExistsByUserName($formData['nick_mail']) === false) {
            $errors['nick-mail'] = $this->lang->t('users', 'user_not_exists');
        } elseif ($this->validate->email($formData['nick_mail']) === true && $this->userModel->resultExistsByEmail($formData['nick_mail']) === false) {
            $errors['nick-mail'] = $this->lang->t('users', 'user_not_exists');
        }
        if ($this->modules->hasPermission('frontend/captcha/index/image') === true && $this->validate->captcha($formData['captcha']) === false) {
            $errors['captcha'] = $this->lang->t('captcha', 'invalid_captcha_entered');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

    /**
     * @param array $formData
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateRegistration(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (empty($formData['nickname'])) {
            $errors['nickname'] = $this->lang->t('system', 'name_to_short');
        }
        if ($this->userModel->resultExistsByUserName($formData['nickname']) === true) {
            $errors['nickname'] = $this->lang->t('users', 'user_name_already_exists');
        }
        if ($this->validate->email($formData['mail']) === false) {
            $errors['mail'] = $this->lang->t('system', 'wrong_email_format');
        }
        if ($this->userModel->resultExistsByEmail($formData['mail']) === true) {
            $errors['mail'] = $this->lang->t('users', 'user_email_already_exists');
        }
        if (empty($formData['pwd']) || empty($formData['pwd_repeat']) || $formData['pwd'] != $formData['pwd_repeat']) {
            $errors[] = $this->lang->t('users', 'type_in_pwd');
        }
        if ($this->modules->hasPermission('frontend/captcha/index/image') === true && $this->validate->captcha($formData['captcha']) === false) {
            $errors['captcha'] = $this->lang->t('captcha', 'invalid_captcha_entered');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

} 