<?php
namespace ACP3\Modules\ACP3\Articles\Validation;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus;
use ACP3\Modules\ACP3\Seo\Validation\ValidationRules\UriAliasValidationRule;

/**
 * Class Validator
 * @package ACP3\Modules\ACP3\Articles\Validation
 */
class FormValidation extends Core\Validation\AbstractFormValidation
{
    /**
     * @var \ACP3\Core\ACL
     */
    protected $acl;

    /**
     * Validator constructor.
     *
     * @param \ACP3\Core\Lang                 $lang
     * @param \ACP3\Core\Validation\Validator $validator
     * @param \ACP3\Core\ACL                  $acl
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validation\Validator $validator,
        Core\ACL $acl)
    {
        parent::__construct($lang, $validator);

        $this->acl = $acl;
    }

    /**
     * @param array  $formData
     * @param string $uriAlias
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validate(array $formData, $uriAlias = '')
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validation\ValidationRules\DateValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => ['start', 'end'],
                    'message' => $this->lang->t('system', 'select_date')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\MinLengthValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'title',
                    'message' => $this->lang->t('articles', 'title_to_short'),
                    'extra' => [
                        'length' => 3
                    ]
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\MinLengthValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'text',
                    'message' => $this->lang->t('articles', 'text_to_short'),
                    'extra' => [
                        'length' => 3
                    ]
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
                ]);
        if ($this->acl->hasPermission('admin/menus/items/create') === true && isset($formData['create']) === true) {
            $this->validateMenuItem($formData);
        }

        $this->validator->validate();
    }

    /**
     * @param array $formData
     */
    protected function validateMenuItem(array $formData)
    {
        $this->validator
            ->addConstraint(
                Core\Validation\ValidationRules\IntegerValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'block_id',
                    'message' => $this->lang->t('menus', 'select_menu_bar')
                ])
            ->addConstraint(
                Menus\Validation\ValidationRules\ParentIdValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'parent_id',
                    'message' => $this->lang->t('menus', 'select_superior_page')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'display',
                    'message' => $this->lang->t('menus', 'select_item_visibility'),
                    'extra' => [
                        'haystack' => [0, 1]
                    ]
                ])
            ->addConstraint(
                Menus\Validation\ValidationRules\AllowedMenuValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => ['parent_id', 'block_id'],
                    'message' => $this->lang->t('menus', 'superior_page_not_allowed')
                ]);
    }
}