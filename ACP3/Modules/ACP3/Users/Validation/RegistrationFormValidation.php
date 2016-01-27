<?php
namespace ACP3\Modules\ACP3\Users\Validation;

use ACP3\Core;
use ACP3\Modules\ACP3\Captcha\Validation\ValidationRules\CaptchaValidationRule;
use ACP3\Modules\ACP3\Users\Validation\ValidationRules\AccountNotExistsByEmailValidationRule;
use ACP3\Modules\ACP3\Users\Validation\ValidationRules\AccountNotExistsByNameValidationRule;

/**
 * Class RegistrationFormValidation
 * @package ACP3\Modules\ACP3\Users\Validation
 */
class RegistrationFormValidation extends AbstractUserFormValidation
{
    /**
     * @inheritdoc
     */
    public function validate(array $formData)
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class)
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'nickname',
                    'message' => $this->translator->t('system', 'name_to_short')
                ])
            ->addConstraint(
                AccountNotExistsByNameValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'nickname',
                    'message' => $this->translator->t('users', 'user_name_already_exists')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\EmailValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'mail',
                    'message' => $this->translator->t('system', 'wrong_email_format')
                ])
            ->addConstraint(
                AccountNotExistsByEmailValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'mail',
                    'message' => $this->translator->t('users', 'user_email_already_exists')
                ])
            ->addConstraint(
                CaptchaValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'captcha',
                    'message' => $this->translator->t('captcha', 'invalid_captcha_entered')
                ]);

        $this->validatePassword($formData, 'pwd', 'pwd_repeat');

        $this->validator->validate();
    }

}