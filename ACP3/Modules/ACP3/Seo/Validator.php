<?php
namespace ACP3\Modules\ACP3\Seo;

use ACP3\Core;

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
        $this->validateFormKey();

        $this->errors = [];
        if ($this->routerValidator->isInternalURI($formData['uri']) === false) {
            $this->errors['uri'] = $this->lang->t('seo', 'type_in_valid_resource');
        }
        if (!empty($formData['alias']) && $this->aliasesValidator->uriAliasExists($formData['alias'], $uriAlias) === true) {
            $this->errors['alias'] = $this->lang->t('seo', 'alias_unallowed_characters_or_exists');
        }

        $this->_checkForFailedValidation();
    }

    /**
     * @param array $formData
     *
     * @throws \ACP3\Core\Exceptions\InvalidFormToken
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateSettings(array $formData)
    {
        $this->validateFormKey();

        $this->errors = [];
        if (empty($formData['title'])) {
            $this->errors['seo-title'] = $this->lang->t('system', 'title_to_short');
        }
        if ($this->validate->isNumber($formData['robots']) === false) {
            $this->errors['seo-robots'] = $this->lang->t('seo', 'select_robots');
        }
        if ($this->validate->isNumber($formData['mod_rewrite']) === false) {
            $this->errors['seo-mod-rewrite'] = $this->lang->t('seo', 'select_mod_rewrite');
        }

        $this->_checkForFailedValidation();
    }
}
