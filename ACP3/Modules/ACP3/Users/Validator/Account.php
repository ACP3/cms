<?php

namespace ACP3\Modules\ACP3\Users\Validator;

use ACP3\Core;
use ACP3\Modules\ACP3\Users\Model\UserRepository;

/**
 * Class Account
 * @package ACP3\Modules\ACP3\Users\Validator
 */
class Account extends AbstractUserValidator
{
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
     * Account constructor.
     *
     * @param \ACP3\Core\Lang                               $lang
     * @param \ACP3\Core\Validator\Validator                $validator
     * @param \ACP3\Core\Validator\Rules\Misc               $validate
     * @param \ACP3\Core\Validator\Rules\Date               $dateValidator
     * @param \ACP3\Core\User                               $user
     * @param \ACP3\Modules\ACP3\Users\Model\UserRepository $userRepository
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Validator $validator,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\Date $dateValidator,
        Core\User $user,
        UserRepository $userRepository
    )
    {
        parent::__construct($lang, $validator, $validate);

        $this->dateValidator = $dateValidator;
        $this->user = $user;
        $this->userModel = $userRepository;
    }

    /**
     * @param array $formData
     *
     * @throws \ACP3\Core\Exceptions\InvalidFormToken
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateEditProfile(array $formData)
    {
        $this->validateFormKey();

        $this->errors = [];
        if (empty($formData['nickname'])) {
            $this->errors['nickname'] = $this->lang->t('system', 'name_to_short');
        }
        if ($this->userModel->resultExistsByUserName($formData['nickname'], $this->user->getUserId()) === true) {
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
        if ($this->userModel->resultExistsByEmail($formData['mail'], $this->user->getUserId()) === true) {
            $this->errors['mail'] = $this->lang->t('users', 'user_email_already_exists');
        }
        if (!empty($formData['icq']) && $this->_icq($formData['icq']) === false) {
            $this->errors['icq'] = $this->lang->t('users', 'invalid_icq_number');
        }
        $this->validateNewPassword($formData, 'new_pwd', 'new_pwd_repeat');

        $this->_checkForFailedValidation();
    }

    /**
     * @param array $formData
     * @param array $settings
     *
     * @throws \ACP3\Core\Exceptions\InvalidFormToken
     * @throws \ACP3\Core\Exceptions\ValidationFailed
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

}