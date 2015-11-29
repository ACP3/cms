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
     * @var \ACP3\Core\User
     */
    protected $user;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\UserRepository
     */
    protected $userModel;

    /**
     * @param \ACP3\Core\Lang                               $lang
     * @param \ACP3\Core\Validator\Validator                $validator
     * @param \ACP3\Core\Validator\Rules\Misc               $validate
     * @param \ACP3\Core\Validator\Rules\ACL                $aclValidator
     * @param \ACP3\Core\Validator\Rules\Date               $dateValidator
     * @param \ACP3\Core\User                               $user
     * @param \ACP3\Modules\ACP3\Users\Model\UserRepository $userRepository
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Validator $validator,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\ACL $aclValidator,
        Core\Validator\Rules\Date $dateValidator,
        Core\User $user,
        UserRepository $userRepository
    )
    {
        parent::__construct($lang, $validator, $validate, $dateValidator);

        $this->aclValidator = $aclValidator;
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
        $this->validator
            ->addConstraint(Core\Validator\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validator\ValidationRules\EmailValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'mail',
                    'message' => $this->lang->t('system', 'wrong_email_format')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'language_override',
                    'message' => $this->lang->t('users', 'select_languages_override')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'entries_override',
                    'message' => $this->lang->t('users', 'select_entries_override')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'enable_registration',
                    'message' => $this->lang->t('users', 'select_enable_registration')
                ]);

        $this->validator->validate();
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

        $this->validateUserSettings($formData, 1, 1);

        if (isset($formData['new_pwd'])) {
            $this->validateNewPassword($formData, 'new_pwd', 'new_pwd_repeat');
        } else {
            $this->validatePassword($formData, 'pwd', 'pwd_repeat');
        }

        $this->_checkForFailedValidation();
    }
}
