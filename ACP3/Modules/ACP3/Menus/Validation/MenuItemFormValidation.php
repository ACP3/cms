<?php
namespace ACP3\Modules\ACP3\Menus\Validation;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;
use ACP3\Modules\ACP3\Menus\Validation\ValidationRules\AllowedMenuValidationRule;
use ACP3\Modules\ACP3\Menus\Validation\ValidationRules\LinkModeValidationRule;
use ACP3\Modules\ACP3\Menus\Validation\ValidationRules\ParentIdValidationRule;
use ACP3\Modules\ACP3\Seo\Validation\ValidationRules\UriAliasValidationRule;

/**
 * Class MenuItemFormValidation
 * @package ACP3\Modules\ACP3\Menus\Validation
 */
class MenuItemFormValidation extends Core\Validation\AbstractFormValidation
{
    /**
     * @inheritdoc
     */
    public function validate(array $formData)
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'mode',
                    'message' => $this->translator->t('menus', 'select_page_type'),
                    'extra' => [
                        'haystack' => [1, 2, 3, 4]
                    ]
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'title',
                    'message' => $this->translator->t('menus', 'title_to_short')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\IntegerValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'block_id',
                    'message' => $this->translator->t('menus', 'select_menu_bar')
                ])
            ->addConstraint(
                ParentIdValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'parent_id',
                    'message' => $this->translator->t('menus', 'select_superior_page')
                ])
            ->addConstraint(
                AllowedMenuValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => ['parent_id', 'block_id'],
                    'message' => $this->translator->t('menus', 'superior_page_not_allowed')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'display',
                    'message' => $this->translator->t('menus', 'select_item_visibility'),
                    'extra' => [
                        'haystack' => [0, 1]
                    ]
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'target',
                    'message' => $this->translator->t('menus', 'type_in_uri_and_target'),
                    'extra' => [
                        'haystack' => [1, 2]
                    ]
                ])
            ->addConstraint(
                LinkModeValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => ['mode', 'module', 'uri', 'articles'],
                    'message' => $this->translator->t('menus', 'type_in_uri_and_target')
                ]);

        if ($formData['mode'] == 2) {
            $this->validator
                ->addConstraint(
                    UriAliasValidationRule::NAME,
                    [
                        'data' => $formData,
                        'field' => 'alias',
                        'message' => $this->translator->t('seo', 'alias_unallowed_characters_or_exists'),
                        'extra' => [
                            'path' => $formData['uri']
                        ]
                    ]);
        }

        $this->validator->validate();
    }
}
