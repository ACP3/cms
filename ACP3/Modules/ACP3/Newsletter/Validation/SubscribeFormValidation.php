<?php
namespace ACP3\Modules\ACP3\Newsletter\Validation;


use ACP3\Core;
use ACP3\Core\Validation\AbstractFormValidation;
use ACP3\Modules\ACP3\Captcha\Validation\ValidationRules\CaptchaValidationRule;
use ACP3\Modules\ACP3\Newsletter\Validation\ValidationRules\AccountExistsValidationRule;

/**
 * Class SubscribeFormValidation
 * @package ACP3\Modules\ACP3\Newsletter\Validation
 */
class SubscribeFormValidation extends AbstractFormValidation
{

    /**
     * @inheritdoc
     */
    public function validate(array $formData)
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'salutation',
                    'message' => $this->translator->t('newsletter', 'select_salutation'),
                    'extra' => [
                        'haystack' => [1, 2]
                    ]
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\EmailValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'mail',
                    'message' => $this->translator->t('system', 'wrong_email_format')
                ])
            ->addConstraint(
                AccountExistsValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'mail',
                    'message' => $this->translator->t('newsletter', 'account_not_exists')
                ])
            ->addConstraint(
                CaptchaValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'captcha',
                    'message' => $this->translator->t('captcha', 'invalid_captcha_entered')
                ]);

        $this->validator->validate();
    }
}