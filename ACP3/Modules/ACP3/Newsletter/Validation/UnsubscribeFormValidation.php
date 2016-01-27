<?php
namespace ACP3\Modules\ACP3\Newsletter\Validation;


use ACP3\Core;
use ACP3\Core\Validation\AbstractFormValidation;
use ACP3\Modules\ACP3\Captcha\Validation\ValidationRules\CaptchaValidationRule;
use ACP3\Modules\ACP3\Newsletter\Validation\ValidationRules\AccountExistsValidationRule;

/**
 * Class UnsubscribeFormValidation
 * @package ACP3\Modules\ACP3\Newsletter\Validation
 */
class UnsubscribeFormValidation extends AbstractFormValidation
{

    /**
     * @inheritdoc
     */
    public function validate(array $formData)
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class)
            ->addConstraint(
                Core\Validation\ValidationRules\EmailValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'mail',
                    'message' => $this->translator->t('system', 'wrong_email_format')
                ])
            ->addConstraint(
                AccountExistsValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'mail',
                    'message' => $this->translator->t('newsletter', 'account_not_exists')
                ])
            ->addConstraint(
                CaptchaValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'captcha',
                    'message' => $this->translator->t('captcha', 'invalid_captcha_entered')
                ]);


        $this->validator->validate();
    }
}