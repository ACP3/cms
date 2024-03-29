<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Validation;

use ACP3\Core;
use ACP3\Core\Helpers\Enum\YesNoEnum;
use ACP3\Modules\ACP3\System\Enum\SiteSubtitleModeEnum;
use ACP3\Modules\ACP3\System\Validation\ValidationRules\IsWysiwygEditorValidationRule;

class AdminSettingsFormValidation extends Core\Validation\AbstractFormValidation
{
    /**
     * @throws Core\Validation\Exceptions\ValidationFailedException
     */
    public function validate(array $formData): void
    {
        $this->validator->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class);

        $this->validateGeneralSettings($formData);
        $this->validateSiteTitleSettings($formData);
        $this->validateDateSettings($formData);
        $this->validateMaintenanceSettings($formData);
        $this->validatePerformanceSettings($formData);
        $this->validateMailerSettings($formData);

        $this->validator->validate();
    }

    /**
     * @param array<string, mixed> $formData
     */
    private function validateGeneralSettings(array $formData): void
    {
        $this->validator
            ->addConstraint(
                Core\Validation\ValidationRules\InternalUriValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'homepage',
                    'message' => $this->translator->t('system', 'incorrect_homepage'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\IntegerValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'entries',
                    'message' => $this->translator->t('system', 'select_records_per_page'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\IntegerValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'flood',
                    'message' => $this->translator->t('system', 'type_in_flood_barrier'),
                ]
            )
            ->addConstraint(
                IsWysiwygEditorValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'wysiwyg',
                    'message' => $this->translator->t('system', 'select_editor'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\LanguagePackExistsValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'language',
                    'message' => $this->translator->t('system', 'select_language'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'mod_rewrite',
                    'message' => $this->translator->t('system', 'select_mod_rewrite'),
                    'extra' => [
                        'haystack' => YesNoEnum::values(),
                    ],
                ]
            );
    }

    /**
     * @param array<string, mixed> $formData
     */
    private function validateSiteTitleSettings(array $formData): void
    {
        $this->validator
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'site_title',
                    'message' => $this->translator->t('system', 'title_to_short'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'site_subtitle_mode',
                    'message' => $this->translator->t('system', 'select_site_subtitle_mode'),
                    'extra' => [
                        'haystack' => SiteSubtitleModeEnum::values(),
                    ],
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'site_subtitle_homepage_mode',
                    'message' => $this->translator->t('system', 'select_site_subtitle_homepage_mode'),
                    'extra' => [
                        'haystack' => YesNoEnum::values(),
                    ],
                ]
            );
    }

    /**
     * @param array<string, mixed> $formData
     */
    private function validateDateSettings(array $formData): void
    {
        $this->validator
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'date_format_long',
                    'message' => $this->translator->t('system', 'type_in_long_date_format'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'date_format_short',
                    'message' => $this->translator->t('system', 'type_in_short_date_format'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\TimeZoneExistsValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'date_time_zone',
                    'message' => $this->translator->t('system', 'select_time_zone'),
                ]
            );
    }

    /**
     * @param array<string, mixed> $formData
     */
    private function validateMaintenanceSettings(array $formData): void
    {
        $this->validator
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'maintenance_mode',
                    'message' => $this->translator->t('system', 'select_online_maintenance'),
                    'extra' => [
                        'haystack' => YesNoEnum::values(),
                    ],
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'maintenance_message',
                    'message' => $this->translator->t('system', 'maintenance_message_to_short'),
                ]
            );
    }

    /**
     * @param array<string, mixed> $formData
     */
    private function validatePerformanceSettings(array $formData): void
    {
        $this->validator
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'page_cache_purge_mode',
                    'message' => $this->translator->t('system', 'select_page_cache_purge_mode'),
                    'extra' => [
                        'haystack' => [1, 2],
                    ],
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'cache_images',
                    'message' => $this->translator->t('system', 'select_cache_images'),
                    'extra' => [
                        'haystack' => YesNoEnum::values(),
                    ],
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\IntegerValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'cache_lifetime',
                    'message' => $this->translator->t('system', 'type_in_cache_lifetime'),
                ]
            );
    }

    /**
     * @param array<string, mixed> $formData
     */
    private function validateMailerSettings(array $formData): void
    {
        $this->validator
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'mailer_type',
                    'message' => $this->translator->t('system', 'select_mailer_type'),
                    'extra' => [
                        'haystack' => ['mail', 'smtp'],
                    ],
                ]
            );

        if ($formData['mailer_type'] === 'smtp') {
            $this->validator
                ->addConstraint(
                    Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                    [
                        'data' => $formData,
                        'field' => 'mailer_smtp_host',
                        'message' => $this->translator->t('system', 'type_in_mailer_smtp_host'),
                    ]
                )
                ->addConstraint(
                    Core\Validation\ValidationRules\IntegerValidationRule::class,
                    [
                        'data' => $formData,
                        'field' => 'mailer_smtp_port',
                        'message' => $this->translator->t('system', 'type_in_mailer_smtp_port'),
                    ]
                )
                ->addConstraint(
                    Core\Validation\ValidationRules\InArrayValidationRule::class,
                    [
                        'data' => $formData,
                        'field' => 'mailer_smtp_security',
                        'message' => $this->translator->t('system', 'select_mailer_smtp_security'),
                        'extra' => [
                            'haystack' => ['none', 'ssl', 'tls'],
                        ],
                    ]
                );

            if ($formData['mailer_smtp_auth'] == 1) {
                $this->validator
                    ->addConstraint(
                        Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                        [
                            'data' => $formData,
                            'field' => 'mailer_smtp_user',
                            'message' => $this->translator->t('system', 'type_in_mailer_smtp_username'),
                        ]
                    );
            }
        }
    }
}
