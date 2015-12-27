<?php
namespace ACP3\Modules\ACP3\Users\Validation;

use ACP3\Core;
use ACP3\Core\Validation\AbstractFormValidation;
use ACP3\Modules\ACP3\Captcha\Validation\ValidationRules\CaptchaValidationRule;
use ACP3\Modules\ACP3\Users\Validation\ValidationRules\AccountNotExistsByEmailValidationRule;
use ACP3\Modules\ACP3\Users\Validation\ValidationRules\AccountNotExistsByNameValidationRule;

/**
 * Class AccountForgotPasswordFormValidation
 * @package ACP3\Modules\ACP3\Users\Validation
 */
class AccountForgotPasswordFormValidation extends AbstractFormValidation
{
    /**
     * @inheritdoc
     */
    public function validate(array $formData)
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
                    'message' => $this->translator->t('users', 'type_in_nickname_or_email')
                ])
            ->addConstraint(
                CaptchaValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'captcha',
                    'message' => $this->translator->t('captcha', 'invalid_captcha_entered')
                ])
            ->addConstraint(
                $ruleName,
                [
                    'data' => $formData,
                    'field' => 'nick_mail',
                    'message' => $this->translator->t('users', 'user_not_exists')
                ]);

        $this->validator->validate();

    }
}