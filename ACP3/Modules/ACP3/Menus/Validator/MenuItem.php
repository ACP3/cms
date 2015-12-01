<?php
namespace ACP3\Modules\ACP3\Menus\Validator;

use ACP3\Core;
use ACP3\Modules\ACP3\Articles;
use ACP3\Modules\ACP3\Menus\Validator\ValidationRules\AllowedMenuValidationRule;
use ACP3\Modules\ACP3\Menus\Validator\ValidationRules\LinkModeValidationRule;
use ACP3\Modules\ACP3\Menus\Validator\ValidationRules\ParentIdValidationRule;
use ACP3\Modules\ACP3\Seo\Validator\ValidationRules\UriAliasValidationRule;

/**
 * Class MenuItem
 * @package ACP3\Modules\ACP3\Menus\Validator
 */
class MenuItem extends Core\Validator\AbstractValidator
{
    /**
     * @param array $formData
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validate(array $formData)
    {
        $this->validator
            ->addConstraint(Core\Validator\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'mode',
                    'message' => $this->lang->t('menus', 'select_page_type'),
                    'extra' => [
                        'haystack' => [1, 2, 3, 4]
                    ]
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'title',
                    'message' => $this->lang->t('menus', 'title_to_short')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\IntegerValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'block_id',
                    'message' => $this->lang->t('menus', 'select_menu_bar')
                ])
            ->addConstraint(
                ParentIdValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'parent_id',
                    'message' => $this->lang->t('menus', 'select_superior_page')
                ])
            ->addConstraint(
                AllowedMenuValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => ['parent_id', 'block_id'],
                    'message' => $this->lang->t('menus', 'superior_page_not_allowed')
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'display',
                    'message' => $this->lang->t('menus', 'select_item_visibility'),
                    'extra' => [
                        'haystack' => [0, 1]
                    ]
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'target',
                    'message' => $this->lang->t('menus', 'type_in_uri_and_target'),
                    'extra' => [
                        'haystack' => [1, 2]
                    ]
                ])
            ->addConstraint(
                LinkModeValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => ['mode', 'module', 'uri', 'articles'],
                    'message' => $this->lang->t('menus', 'type_in_uri_and_target')
                ]);

        if ($formData['mode'] == 2) {
            $this->validator
                ->addConstraint(
                    UriAliasValidationRule::NAME,
                    [
                        'data' => $formData,
                        'field' => 'alias',
                        'message' => $this->lang->t('seo', 'alias_unallowed_characters_or_exists'),
                        'extra' => [
                            'path' => $formData['uri']
                        ]
                    ]);
        }

        $this->validator->validate();
    }
}
