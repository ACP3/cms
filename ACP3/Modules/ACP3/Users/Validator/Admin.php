<?php
namespace ACP3\Modules\ACP3\Users\Validator;

use ACP3\Core;
use ACP3\Modules\ACP3\Users\Model\UserRepository;

/**
 * Class Admin
 * @package ACP3\Modules\ACP3\Users\Validator
 */
class Admin extends AbstractUserValidator
{
    /**
     * @var \ACP3\Core\Validator\Rules\ACL
     */
    protected $aclValidator;
    /**
     * @var \ACP3\Core\Validator\Rules\Date
     */
    protected $dateValidator;
    /**
     * @var \ACP3\Core\User
     */
    protected $user;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\UserRepository
     */
    protected $userModel;

    /**
     * @param \ACP3\Core\Lang                               $lang
     * @param \ACP3\Core\Validator\Rules\Misc               $validate
     * @param \ACP3\Core\Validator\Rules\ACL                $aclValidator
     * @param \ACP3\Core\Validator\Rules\Date               $dateValidator
     * @param \ACP3\Core\User                               $user
     * @param \ACP3\Modules\ACP3\Users\Model\UserRepository $userRepository
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\ACL $aclValidator,
        Core\Validator\Rules\Date $dateValidator,
        Core\User $user,
        UserRepository $userRepository
    )
    {
        parent::__construct($lang, $validate);

        $this->aclValidator = $aclValidator;
        $this->dateValidator = $dateValidator;
        $this->user = $user;
        $this->userModel = $userRepository;
    }

    /**
     * @param array $formData
     *
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
     * @param int   $userId
     *
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
            $this->validateNewPassword($formData, 'new_pwd', 'new_pwd_repeat');
        } else {
            $this->validatePassword($formData, 'pwd', 'pwd_repeat');
        }

        $this->_checkForFailedValidation();
    }
}
