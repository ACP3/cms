<?php
namespace ACP3\Modules\ACP3\Users\Validation;

use ACP3\Core;
use ACP3\Core\Validation\AbstractFormValidation;

/**
 * Class AdminSettingsFormValidation
 * @package ACP3\Modules\ACP3\Users\Validation
 */
class AdminSettingsFormValidation extends AbstractFormValidation
{

    /**
     * @inheritdoc
     */
    public function validate(array $formData)
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validation\ValidationRules\EmailValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'mail',
                    'message' => $this->translator->t('system', 'wrong_email_format')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'language_override',
                    'message' => $this->translator->t('users', 'select_languages_override')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'entries_override',
                    'message' => $this->translator->t('users', 'select_entries_override')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'enable_registration',
                    'message' => $this->translator->t('users', 'select_enable_registration')
                ]);

        $this->validator->validate();
    }
}