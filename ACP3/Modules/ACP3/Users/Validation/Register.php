<?php
namespace ACP3\Modules\ACP3\Users\Validation;

use ACP3\Core;
use ACP3\Modules\ACP3\Captcha\Validation\ValidationRules\CaptchaValidationRule;
use ACP3\Modules\ACP3\Users\Validation\ValidationRules\AccountNotExistsByEmailValidationRule;
use ACP3\Modules\ACP3\Users\Validation\ValidationRules\AccountNotExistsByNameValidationRule;

/**
 * Class Register
 * @package ACP3\Modules\ACP3\Users\Validator
 */
class Register extends AbstractUserFormValidation
{
    /**
     * @param array $formData
     *
     * @throws \ACP3\Core\Exceptions\InvalidFormToken
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateForgotPassword(array $formData)
    {
        if ($this->validator->is(Core\Validation\ValidationRules\EmailValidationRule::NAME, $formData['nick_mail'])) {
            $ruleName = AccountNotExistsByEmailValidationRule::NAME;
        } else {
            $ruleName = AccountNotExistsByNameValidationRule::NAME;
        }

        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'nick_mail',
                    'message' => $this->lang->t('users', 'type_in_nickname_or_email')
                ])
            ->addConstraint(
                CaptchaValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'captcha',
                    'message' => $this->lang->t('captcha', 'invalid_captcha_entered')
                ])
            ->addConstraint(
                $ruleName,
                [
                    'data' => $formData,
                    'field' => 'nick_mail',
                    'message' => $this->lang->t('users', 'user_not_exists')
                ]);

        $this->validator->validate();
    }

    /**
     * @param array $formData
     *
     * @throws \ACP3\Core\Exceptions\InvalidFormToken
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateRegistration(array $formData)
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'nickname',
                    'message' => $this->lang->t('system', 'name_to_short')
                ])
            ->addConstraint(
                AccountNotExistsByNameValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'nickname',
                    'message' => $this->lang->t('users', 'user_name_already_exists')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\EmailValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'mail',
                    'message' => $this->lang->t('system', 'wrong_email_format')
                ])
            ->addConstraint(
                AccountNotExistsByEmailValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'mail',
                    'message' => $this->lang->t('users', 'user_email_already_exists')
                ])
            ->addConstraint(
                CaptchaValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'captcha',
                    'message' => $this->lang->t('captcha', 'invalid_captcha_entered')
                ]);

        $this->validatePassword($formData, 'pwd', 'pwd_repeat');

        $this->validator->validate();
    }

}