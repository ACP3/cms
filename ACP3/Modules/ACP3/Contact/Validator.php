<?php

namespace ACP3\Modules\ACP3\Contact;

use ACP3\Core;
use ACP3\Modules\ACP3\Captcha\Validator\ValidationRules\CaptchaValidationRule;

/**
 * Class Validator
 * @package ACP3\Modules\ACP3\Contact
 */
class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @var \ACP3\Core\Validator\Validator
     */
    protected $validator;

    /**
     * Validator constructor.
     *
     * @param \ACP3\Core\Lang                 $lang
     * @param \ACP3\Core\Validator\Validator  $validator
     * @param \ACP3\Core\Validator\Rules\Misc $validate
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Validator $validator,
        Core\Validator\Rules\Misc $validate
    )
    {
        parent::__construct($lang, $validate);

        $this->validator = $validator;
    }

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
                    'field' => 'name',
                    'message' => $this->lang->t('system', 'name_to_short')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\EmailValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'mail',
                    'message' => $this->lang->t('system', 'wrong_email_format')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'message',
                    'message' => $this->lang->t('system', 'message_to_short')
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
}
