<?php
namespace ACP3\Modules\ACP3\Newsletter;

use ACP3\Core;
use ACP3\Modules\ACP3\Captcha\Validator\ValidationRules\CaptchaValidationRule;
use ACP3\Modules\ACP3\Newsletter\Validator\ValidationRules\AccountExistsByHashValidationRule;
use ACP3\Modules\ACP3\Newsletter\Validator\ValidationRules\AccountExistsValidationRule;

/**
 * Class Validator
 * @package ACP3\Modules\ACP3\Newsletter
 */
class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @param array $formData
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validate(array $formData)
    {
        $this->validator
            ->addConstraint(Core\Validator\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validator\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'title',
                    'message' => $this->lang->t('newsletter', 'subject_to_short')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'text',
                    'message' => $this->lang->t('newsletter', 'text_to_short')
                ]);

        $this->validator->validate();
    }

    /**
     * @param array $formData
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateSubscribe(array $formData)
    {
        $this->validator
            ->addConstraint(Core\Validator\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'salutation',
                    'message' => $this->lang->t('newsletter', 'select_salutation'),
                    'extra' => [
                        'haystack' => [1, 2]
                    ]
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\EmailValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'mail',
                    'message' => $this->lang->t('system', 'wrong_email_format')
                ])
            ->addConstraint(
                AccountExistsValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'mail',
                    'message' => $this->lang->t('newsletter', 'account_not_exists')
                ])
            ->addConstraint(
                CaptchaValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'captcha',
                    'message' => $this->lang->t('captcha', 'invalid_captcha_entered')
                ]);

        $this->validator->validate();
    }

    /**
     * @param array $formData
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateUnsubscribe(array $formData)
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
                AccountExistsValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'mail',
                    'message' => $this->lang->t('newsletter', 'account_not_exists')
                ])
            ->addConstraint(
                CaptchaValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'captcha',
                    'message' => $this->lang->t('captcha', 'invalid_captcha_entered')
                ]);


        $this->validator->validate();
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
                ]);

        $this->validator->validate();
    }

    /**
     * @param string $hash
     *
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validateActivate($hash)
    {
        $this->validator
            ->addConstraint(
                AccountExistsByHashValidationRule::NAME,
                [
                    'data' => $hash,
                    'message' => $this->lang->t('newsletter', 'account_not_exists')
                ]);

        $this->validator->validate();
    }
}
