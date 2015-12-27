<?php
namespace ACP3\Modules\ACP3\Seo\Validation;

use ACP3\Core;
use ACP3\Modules\ACP3\Seo\Validation\ValidationRules\UriAliasValidationRule;

/**
 * Class AdminFormValidation
 * @package ACP3\Modules\ACP3\Seo\Validation
 */
class AdminFormValidation extends Core\Validation\AbstractFormValidation
{
    /**
     * @var string
     */
    protected $uriAlias = '';

    /**
     * @param string $uriAlias
     *
     * @return $this
     */
    public function setUriAlias($uriAlias)
    {
        $this->uriAlias = $uriAlias;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function validate(array $formData)
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validation\ValidationRules\InternalUriValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'uri',
                    'message' => $this->translator->t('seo', 'type_in_valid_resource')
                ])
            ->addConstraint(
                UriAliasValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'alias',
                    'message' => $this->translator->t('seo', 'alias_unallowed_characters_or_exists'),
                    'extra' => [
                        'path' => $this->uriAlias
                    ]
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'seo_robots',
                    'message' => $this->translator->t('seo', 'select_robots'),
                    'extra' => [
                        'haystack' => [0, 1, 2, 3, 4]
                    ]
                ]);

        $this->validator->validate();
    }
}
