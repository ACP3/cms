<?php
namespace ACP3\Modules\ACP3\Seo;

use ACP3\Core;
use ACP3\Modules\ACP3\Seo\Validator\ValidationRules\UriAliasValidationRule;

/**
 * Class Validator
 * @package ACP3\Modules\ACP3\Seo
 */
class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @var Core\Validator\Rules\Router
     */
    protected $routerValidator;
    /**
     * @var Core\Validator\Rules\Router\Aliases
     */
    protected $aliasesValidator;

    /**
     * Validator constructor.
     *
     * @param \ACP3\Core\Lang                           $lang
     * @param \ACP3\Core\Validator\Validator            $validator
     * @param \ACP3\Core\Validator\Rules\Misc           $validate
     * @param \ACP3\Core\Validator\Rules\Router         $routerValidator
     * @param \ACP3\Core\Validator\Rules\Router\Aliases $aliasesValidator
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Validator $validator,
        Core\Validator\Rules\Misc $validate,
        Core\Validator\Rules\Router $routerValidator,
        Core\Validator\Rules\Router\Aliases $aliasesValidator)
    {
        parent::__construct($lang, $validator, $validate);

        $this->routerValidator = $routerValidator;
        $this->aliasesValidator = $aliasesValidator;

    }

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
            ->addConstraint(Core\Validator\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validator\ValidationRules\InternalUriValidationRule::NAME,
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
                Core\Validator\ValidationRules\InArrayValidationRule::NAME,
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
            ->addConstraint(Core\Validator\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validator\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'title',
                    'message' => $this->lang->t('system', 'title_to_short'),
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'robots',
                    'message' => $this->lang->t('seo', 'select_robots'),
                    'extra' => [
                        'haystack' => [1, 2, 3, 4]
                    ]
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\InArrayValidationRule::NAME,
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
