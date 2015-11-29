<?php
namespace ACP3\Modules\ACP3\Search;

use ACP3\Core;

/**
 * Class Validator
 * @package ACP3\Modules\ACP3\Search
 */
class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @param array $formData
     *
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validate(array $formData)
    {
        $this->validator
            ->addConstraint(
                Core\Validator\ValidationRules\MinLengthValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'search_term',
                    'message' => $this->lang->t('search', 'search_term_to_short'),
                    'extra' => [
                        'length' => 4
                    ]
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'mods',
                    'message' => $this->lang->t('search', 'no_module_selected')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'area',
                    'message' => $this->lang->t('search', 'no_area_selected')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'sort',
                    'message' => $this->lang->t('search', 'no_sorting_selected'),
                    'extra' => [
                        'haystack' => ['asc', 'desc']
                    ]
                ]);

        $this->validator->validate();
    }
}
