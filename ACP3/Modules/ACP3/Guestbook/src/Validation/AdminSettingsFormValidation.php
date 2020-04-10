<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Validation;

use ACP3\Core;
use ACP3\Core\Validation\AbstractFormValidation;

class AdminSettingsFormValidation extends AbstractFormValidation
{
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;

    public function __construct(
        Core\I18n\Translator $translator,
        Core\Validation\Validator $validator,
        Core\Modules $modules
    ) {
        parent::__construct($translator, $validator);

        $this->modules = $modules;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(array $formData)
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class)
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'dateformat',
                    'message' => $this->translator->t('system', 'select_date_format'),
                    'extra' => [
                        'haystack' => ['long', 'short'],
                    ],
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'notify',
                    'message' => $this->translator->t('guestbook', 'select_notification_type'),
                    'extra' => [
                        'haystack' => [0, 1, 2],
                    ],
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'overlay',
                    'message' => $this->translator->t('guestbook', 'select_use_overlay'),
                    'extra' => [
                        'haystack' => [0, 1],
                    ],
                ]
            );

        if ($formData['notify'] != 0) {
            $this->validator
                ->addConstraint(
                    Core\Validation\ValidationRules\EmailValidationRule::class,
                    [
                        'data' => $formData,
                        'field' => 'notify_email',
                        'message' => $this->translator->t('system', 'wrong_email_format'),
                    ]
                );
        }

        if ($this->modules->isActive('newsletter') === true) {
            $this->validator
                ->addConstraint(
                    Core\Validation\ValidationRules\InArrayValidationRule::class,
                    [
                        'data' => $formData,
                        'field' => 'newsletter_integration',
                        'message' => $this->translator->t('guestbook', 'select_newsletter_integration'),
                        'extra' => [
                            'haystack' => [0, 1],
                        ],
                    ]
                );
        }

        $this->validator->validate();
    }
}
