<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Validation;

use ACP3\Core;
use ACP3\Modules\ACP3\Guestbook\Validation\ValidationRules\FloodBarrierValidationRule;

class FormValidation extends Core\Validation\AbstractFormValidation
{
    /**
     * @var string
     */
    private $ipAddress = '';

    /**
     * @return $this
     */
    public function setIpAddress(string $ipAddress)
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \ACP3\Core\Validation\Exceptions\ValidationRuleNotFoundException
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
                        'ip' => $this->ipAddress,
                    ],
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'name',
                    'message' => $this->translator->t('system', 'name_to_short'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'message',
                    'message' => $this->translator->t('system', 'message_to_short'),
                ]
            );

        if (!empty($formData['mail'])) {
            $this->validator
                ->addConstraint(
                    Core\Validation\ValidationRules\EmailValidationRule::class,
                    [
                        'data' => $formData,
                        'field' => 'mail',
                        'message' => $this->translator->t('system', 'wrong_email_format'),
                    ]
                );
        }

        $this->validator->dispatchValidationEvent('guestbook.validation.create', $formData);
        $this->validator->dispatchValidationEvent('captcha.validation.validate_captcha', $formData);

        $this->validator->validate();
    }
}
