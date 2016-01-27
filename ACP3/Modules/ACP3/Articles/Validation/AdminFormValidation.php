<?php
namespace ACP3\Modules\ACP3\Articles\Validation;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus;
use ACP3\Modules\ACP3\Seo\Validation\ValidationRules\UriAliasValidationRule;

/**
 * Class AdminFormValidation
 * @package ACP3\Modules\ACP3\Articles\Validation
 */
class AdminFormValidation extends Core\Validation\AbstractFormValidation
{
    /**
     * @var \ACP3\Core\ACL
     */
    protected $acl;
    /**
     * @var string
     */
    protected $uriAlias = '';

    /**
     * Validator constructor.
     *
     * @param \ACP3\Core\I18n\Translator      $translator
     * @param \ACP3\Core\Validation\Validator $validator
     * @param \ACP3\Core\ACL                  $acl
     */
    public function __construct(
        Core\I18n\Translator $translator,
        Core\Validation\Validator $validator,
        Core\ACL $acl)
    {
        parent::__construct($translator, $validator);

        $this->acl = $acl;
    }

    /**
     * @param $uriAlias
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
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class)
            ->addConstraint(
                Core\Validation\ValidationRules\DateValidationRule::class,
                [
                    'data' => $formData,
                    'field' => ['start', 'end'],
                    'message' => $this->translator->t('system', 'select_date')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\MinLengthValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'title',
                    'message' => $this->translator->t('articles', 'title_to_short'),
                    'extra' => [
                        'length' => 3
                    ]
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\MinLengthValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'text',
                    'message' => $this->translator->t('articles', 'text_to_short'),
                    'extra' => [
                        'length' => 3
                    ]
                ])
            ->addConstraint(
                UriAliasValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'alias',
                    'message' => $this->translator->t('seo', 'alias_unallowed_characters_or_exists'),
                    'extra' => [
                        'path' => $this->uriAlias
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
                Core\Validation\ValidationRules\IntegerValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'block_id',
                    'message' => $this->translator->t('menus', 'select_menu_bar')
                ])
            ->addConstraint(
                Menus\Validation\ValidationRules\ParentIdValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'parent_id',
                    'message' => $this->translator->t('menus', 'select_superior_page')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'display',
                    'message' => $this->translator->t('menus', 'select_item_visibility'),
                    'extra' => [
                        'haystack' => [0, 1]
                    ]
                ])
            ->addConstraint(
                Menus\Validation\ValidationRules\AllowedMenuValidationRule::class,
                [
                    'data' => $formData,
                    'field' => ['parent_id', 'block_id'],
                    'message' => $this->translator->t('menus', 'superior_page_not_allowed')
                ]);
    }
}