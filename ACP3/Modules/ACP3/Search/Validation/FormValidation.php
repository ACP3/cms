<?php
namespace ACP3\Modules\ACP3\Search\Validation;

use ACP3\Core;

/**
 * Class FormValidation
 * @package ACP3\Modules\ACP3\Search\Validation
 */
class FormValidation extends Core\Validation\AbstractFormValidation
{
    /**
     * @inheritdoc
     */
    public function validate(array $formData)
    {
        $this->validator
            ->addConstraint(
                Core\Validation\ValidationRules\MinLengthValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'search_term',
                    'message' => $this->translator->t('search', 'search_term_to_short'),
                    'extra' => [
                        'length' => 4
                    ]
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'mods',
                    'message' => $this->translator->t('search', 'no_module_selected')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'area',
                    'message' => $this->translator->t('search', 'no_area_selected')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'sort',
                    'message' => $this->translator->t('search', 'no_sorting_selected'),
                    'extra' => [
                        'haystack' => ['asc', 'desc']
                    ]
                ]);

        $this->validator->validate();
    }
}
