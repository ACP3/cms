<?php
namespace ACP3\Modules\ACP3\System\Validator;

use ACP3\Core;
use ACP3\Modules\ACP3\System\Validator\ValidationRules\IsWysiwygEditorValidationRule;

/**
 * Class Settings
 * @package ACP3\Modules\ACP3\System\Validator
 */
class Settings extends Core\Validator\AbstractValidator
{
    /**
     * @param array $formData
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validate(array $formData)
    {
        $this->validator->addConstraint(Core\Validator\ValidationRules\FormTokenValidationRule::NAME);

        $this->validateGeneralSettings($formData);
        $this->validateDateSettings($formData);
        $this->validateMaintenanceSettings($formData);
        $this->validatePerformanceSettings($formData);
        $this->validateMailerSettings($formData);

        $this->validator->validate();
    }

    /**
     * @param array $formData
     */
    protected function validateGeneralSettings(array $formData)
    {
        $this->validator
            ->addConstraint(
                Core\Validator\ValidationRules\InternalUriValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'homepage',
                    'message' => $this->lang->t('system', 'incorrect_homepage')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\IntegerValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'entries',
                    'message' => $this->lang->t('system', 'select_records_per_page')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\IntegerValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'flood',
                    'message' => $this->lang->t('system', 'type_in_flood_barrier')
                ])
            ->addConstraint(
                IsWysiwygEditorValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'wysiwyg',
                    'message' => $this->lang->t('system', 'select_editor')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\LanguagePackExistsValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'language',
                    'message' => $this->lang->t('system', 'select_language')
                ]);
    }

    /**
     * @param array $formData
     */
    protected function validateDateSettings(array $formData)
    {
        $this->validator
            ->addConstraint(
                Core\Validator\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'date_format_long',
                    'message' => $this->lang->t('system', 'type_in_long_date_format')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'date_format_short',
                    'message' => $this->lang->t('system', 'type_in_short_date_format')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\TimeZoneExistsValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'date_time_zone',
                    'message' => $this->lang->t('system', 'select_time_zone')
                ]);
    }

    /**
     * @param array $formData
     */
    protected function validateMaintenanceSettings(array $formData)
    {
        $this->validator
            ->addConstraint(
                Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'maintenance_mode',
                    'message' => $this->lang->t('system', 'select_online_maintenance'),
                    'extra' => [
                        'haystack' => [0, 1]
                    ]
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'maintenance_message',
                    'message' => $this->lang->t('system', 'maintenance_message_to_short')
                ]);
    }

    /**
     * @param array $formData
     */
    protected function validatePerformanceSettings(array $formData)
    {
        $this->validator
            ->addConstraint(
                Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'cache_images',
                    'message' => $this->lang->t('system', 'select_cache_images'),
                    'extra' => [
                        'haystack' => [0, 1]
                    ]
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\IntegerValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'cache_minify',
                    'message' => $this->lang->t('system', 'type_in_minify_cache_lifetime')
                ]);
    }

    /**
     * @param array $formData
     */
    protected function validateMailerSettings(array $formData)
    {
        $this->validator
            ->addConstraint(
                Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'mailer_type',
                    'message' => $this->lang->t('system', 'select_mailer_type'),
                    'extra' => [
                        'haystack' => ['mail', 'smtp']
                    ]
                ]);

        if ($formData['mailer_type'] === 'smtp') {
            $this->validator
                ->addConstraint(
                    Core\Validator\ValidationRules\NotEmptyValidationRule::NAME,
                    [
                        'data' => $formData,
                        'field' => 'mailer_smtp_host',
                        'message' => $this->lang->t('system', 'type_in_mailer_smtp_host')
                    ])
                ->addConstraint(
                    Core\Validator\ValidationRules\IntegerValidationRule::NAME,
                    [
                        'data' => $formData,
                        'field' => 'mailer_smtp_port',
                        'message' => $this->lang->t('system', 'type_in_mailer_smtp_port')
                    ]);

            if ($formData['mailer_smtp_auth'] == 1) {
                $this->validator
                    ->addConstraint(
                        Core\Validator\ValidationRules\NotEmptyValidationRule::NAME,
                        [
                            'data' => $formData,
                            'field' => 'mailer_smtp_user',
                            'message' => $this->lang->t('system', 'type_in_mailer_smtp_username')
                        ]);
            }
        }
    }
}
