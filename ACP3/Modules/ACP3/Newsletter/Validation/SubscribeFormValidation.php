<?php
namespace ACP3\Modules\ACP3\Newsletter\Validation;

use ACP3\Core;
use ACP3\Core\Validation\AbstractFormValidation;
use ACP3\Modules\ACP3\Newsletter\Validation\ValidationRules\AccountNotExistsValidationRule;

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
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class)
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'salutation',
                    'message' => $this->translator->t('newsletter', 'select_salutation'),
                    'extra' => [
                        'haystack' => [0, 1, 2]
                    ]
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\EmailValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'mail',
                    'message' => $this->translator->t('system', 'wrong_email_format')
                ])
            ->addConstraint(
                AccountNotExistsValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'mail',
                    'message' => $this->translator->t('newsletter', 'account_exists')
                ]);

        $this->validator->dispatchValidationEvent('captcha.validation.validate_captcha', $formData);

        $this->validator->validate();
    }
}
