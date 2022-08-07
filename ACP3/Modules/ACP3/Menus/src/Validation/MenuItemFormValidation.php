<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Validation;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus\Enum\PageTypeEnum;
use ACP3\Modules\ACP3\Menus\Validation\ValidationRules\AllowedMenuValidationRule;
use ACP3\Modules\ACP3\Menus\Validation\ValidationRules\LinkModeValidationRule;
use ACP3\Modules\ACP3\Menus\Validation\ValidationRules\ParentIdValidationRule;

class MenuItemFormValidation extends Core\Validation\AbstractFormValidation
{
    /**
     * {@inheritdoc}
     */
    public function validate(array $formData): void
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class)
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'mode',
                    'message' => $this->translator->t('menus', 'select_page_type'),
                    'extra' => [
                        'haystack' => PageTypeEnum::values(),
                    ],
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'title',
                    'message' => $this->translator->t('menus', 'title_to_short'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\IntegerValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'block_id',
                    'message' => $this->translator->t('menus', 'select_menu_bar'),
                ]
            )
            ->addConstraint(
                ParentIdValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'parent_id',
                    'message' => $this->translator->t('menus', 'select_superior_page'),
                ]
            )
            ->addConstraint(
                AllowedMenuValidationRule::class,
                [
                    'data' => $formData,
                    'field' => ['parent_id', 'block_id'],
                    'message' => $this->translator->t('menus', 'superior_page_not_allowed'),
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'display',
                    'message' => $this->translator->t('menus', 'select_item_visibility'),
                    'extra' => [
                        'haystack' => [0, 1],
                    ],
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'target',
                    'message' => $this->translator->t('menus', 'type_in_uri_and_target'),
                    'extra' => [
                        'haystack' => [1, 2],
                    ],
                ]
            )
            ->addConstraint(
                LinkModeValidationRule::class,
                [
                    'data' => $formData,
                    'field' => ['mode', 'module', 'uri'],
                    'message' => $this->translator->t('menus', 'type_in_uri_and_target'),
                ]
            );

        $this->validator->validate();
    }
}
