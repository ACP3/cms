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
        parent::__construct($lang, $validator, $validate, $dateValidator);

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
        parent::validateUserSettings($formData, $settings['language_override'], $settings['entries_override']);

        $this->_checkForFailedValidation();
    }

}