<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Validation;

use ACP3\Core;
use ACP3\Core\Helpers\Enum\YesNoEnum;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\Validation\AbstractFormValidation;
use ACP3\Core\Validation\Validator;
use ACP3\Modules\ACP3\Guestbook\Installer\Schema;

class AdminFormValidation extends AbstractFormValidation
{
    public function __construct(Translator $translator, Validator $validator, private readonly SettingsInterface $settings)
    {
        parent::__construct($translator, $validator);
    }

    /**
     * @param array<string, mixed> $settings
     *
     * @deprecated since ACP3 version 6.6.0. Will be removed with version 7.0.0.
     */
    public function setSettings(array $settings): static
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(array $formData): void
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class)
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'message',
                    'message' => $this->translator->t('system', 'message_to_short'),
                ]
            );

        if ($settings['notify'] == 2) {
            $this->validator
                ->addConstraint(
                    Core\Validation\ValidationRules\InArrayValidationRule::class,
                    [
                        'data' => $formData,
                        'field' => 'active',
                        'message' => $this->translator->t('guestbook', 'select_activate'),
                        'extra' => [
                            'haystack' => YesNoEnum::values(),
                        ],
                    ]
                );
        }

        $this->validator->validate();
    }
}
