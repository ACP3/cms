<?php
namespace ACP3\Modules\ACP3\Comments\Validation;

use ACP3\Core;
use ACP3\Modules\ACP3\Captcha\Validation\ValidationRules\CaptchaValidationRule;
use ACP3\Modules\ACP3\Comments\Validation\ValidationRules\FloodBarrierValidationRule;

/**
 * Class FormValidation
 * @package ACP3\Modules\ACP3\Comments\Validation
 */
class FormValidation extends Core\Validation\AbstractFormValidation
{
    protected $ipAddress = '';

    /**
     * @param $ipAddress
     *
     * @return $this
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function validate(array $formData)
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class)
            ->addConstraint(
                FloodBarrierValidationRule::class,
                [
                    'message' => $this->translator->t('system', 'flood_no_entry_possible'),
                    'extra' => [
                        'ip' => $this->ipAddress
                    ]
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'name',
                    'message' => $this->translator->t('system', 'name_to_short')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'message',
                    'message' => $this->translator->t('system', 'message_to_short')
                ])
            ->addConstraint(
                CaptchaValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'captcha',
                    'message' => $this->translator->t('captcha', 'invalid_captcha_entered')
                ]
            );

        $this->validator->validate();
    }
}
