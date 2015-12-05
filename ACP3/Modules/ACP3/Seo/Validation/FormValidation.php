<?php
namespace ACP3\Modules\ACP3\Seo\Validation;

use ACP3\Core;
use ACP3\Modules\ACP3\Seo\Validation\ValidationRules\UriAliasValidationRule;

/**
 * Class Validator
 * @package ACP3\Modules\ACP3\Seo\Validation
 */
class FormValidation extends Core\Validation\AbstractFormValidation
{
    /**
     * @param array  $formData
     * @param string $uriAlias
     *
     * @throws \ACP3\Core\Exceptions\InvalidFormToken
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validate(array $formData, $uriAlias = '')
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validation\ValidationRules\InternalUriValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'uri',
                    'message' => $this->lang->t('seo', 'type_in_valid_resource')
                ])
            ->addConstraint(
                UriAliasValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'alias',
                    'message' => $this->lang->t('seo', 'alias_unallowed_characters_or_exists'),
                    'extra' => [
                        'path' => $uriAlias
                    ]
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'seo_robots',
                    'message' => $this->lang->t('seo', 'select_robots'),
                    'extra' => [
                        'haystack' => [0, 1, 2, 3, 4]
                    ]
                ]);

        $this->validator->validate();
    }

    /**
     * @param array $formData
     *
     * @throws \ACP3\Core\Exceptions\InvalidFormToken
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateSettings(array $formData)
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'title',
                    'message' => $this->lang->t('system', 'title_to_short'),
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'robots',
                    'message' => $this->lang->t('seo', 'select_robots'),
                    'extra' => [
                        'haystack' => [1, 2, 3, 4]
                    ]
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'mod_rewrite',
                    'message' => $this->lang->t('seo', 'select_mod_rewrite'),
                    'extra' => [
                        'haystack' => [0, 1]
                    ]
                ]);

        $this->validator->validate();
    }
}
