<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Validation;

use ACP3\Core;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Validation\Validator;
use ACP3\Modules\ACP3\Guestbook\Installer\Schema;
use ACP3\Modules\ACP3\Guestbook\Validation\ValidationRules\FloodBarrierValidationRule;
use ACP3\Modules\ACP3\Newsletter;

class FormValidation extends Core\Validation\AbstractFormValidation
{
    /**
     * @var string
     */
    protected $ipAddress = '';
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;

    public function __construct(
        Translator $translator,
        Validator $validator,
        Core\Settings\SettingsInterface $settings)
    {
        parent::__construct($translator, $validator);

        $this->settings = $settings;
    }

    /**
     * @param string $ipAddress
     *
     * @return $this
     */
    public function setIpAddress($ipAddress)
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
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

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

            if ($settings['newsletter_integration'] == 1 && isset($formData['subscribe_newsletter'])) {
                $this->validator
                    ->addConstraint(
                        Newsletter\Validation\ValidationRules\AccountExistsValidationRule::class,
                        [
                            'data' => $formData,
                            'field' => 'mail',
                            'message' => $this->translator->t('newsletter', 'account_exists'),
                        ]
                    );
            }
        }

        $this->validator->dispatchValidationEvent('captcha.validation.validate_captcha', $formData);

        $this->validator->validate();
    }
}
