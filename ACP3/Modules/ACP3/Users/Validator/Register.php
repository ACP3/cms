<?php
namespace ACP3\Modules\ACP3\Users\Validator;

use ACP3\Core;
use ACP3\Modules\ACP3\Users\Model\UserRepository;

/**
 * Class Register
 * @package ACP3\Modules\ACP3\Users\Validator
 */
class Register extends AbstractUserValidator
{
    /**
     * @var \ACP3\Core\Validator\Rules\Captcha
     */
    protected $captchaValidator;
    /**
     * @var \ACP3\Core\ACL
     */
    protected $acl;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model\UserRepository
     */
    protected $userModel;

    /**
     * @param \ACP3\Core\Lang                               $lang
     * @param \ACP3\Core\Validator\Validator                $validator
     * @param \ACP3\Core\Validator\Rules\Misc               $validate
     * @param \ACP3\Core\Validator\Rules\Captcha            $captchaValidator
     * @param \ACP3\Core\ACL                                $acl
     * @param \ACP3\Modules\ACP3\Users\Model\UserRepository $userRepository
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Validator $validator,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\Captcha $captchaValidator,
        Core\ACL $acl,
        UserRepository $userRepository
    )
    {
        parent::__construct($lang, $validator, $validate);

        $this->captchaValidator = $captchaValidator;
        $this->acl = $acl;
        $this->userModel = $userRepository;
    }

    /**
     * @param array $formData
     *
     * @throws \ACP3\Core\Exceptions\InvalidFormToken
     * @throws \ACP3\Core\Exceptions\ValidationFailed
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
     *
     * @throws \ACP3\Core\Exceptions\InvalidFormToken
     * @throws \ACP3\Core\Exceptions\ValidationFailed
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
        $this->validatePassword($formData, 'pwd', 'pwd_repeat');
        if ($this->acl->hasPermission('frontend/captcha/index/image') === true && $this->captchaValidator->captcha($formData['captcha']) === false) {
            $this->errors['captcha'] = $this->lang->t('captcha', 'invalid_captcha_entered');
        }

        $this->_checkForFailedValidation();
    }

}