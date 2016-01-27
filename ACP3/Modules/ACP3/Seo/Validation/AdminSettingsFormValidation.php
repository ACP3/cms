<?php
namespace ACP3\Modules\ACP3\Seo\Validation;

use ACP3\Core;
use ACP3\Core\Validation\AbstractFormValidation;

/**
 * Class AdminSettingsFormValidation
 * @package ACP3\Modules\ACP3\Seo\Validation
 */
class AdminSettingsFormValidation extends AbstractFormValidation
{
    /**
     * @inheritdoc
     */
    public function validate(array $formData)
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class)
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'title',
                    'message' => $this->translator->t('system', 'title_to_short'),
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'robots',
                    'message' => $this->translator->t('seo', 'select_robots'),
                    'extra' => [
                        'haystack' => [1, 2, 3, 4]
                    ]
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'mod_rewrite',
                    'message' => $this->translator->t('seo', 'select_mod_rewrite'),
                    'extra' => [
                        'haystack' => [0, 1]
                    ]
                ]);

        $this->validator->validate();
    }
}