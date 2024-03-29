<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Validation;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus\Validation\ValidationRules\MenuAlreadyExistsValidationRule;
use ACP3\Modules\ACP3\Menus\Validation\ValidationRules\MenuNameValidationRule;

class MenuFormValidation extends Core\Validation\AbstractFormValidation
{
    private int $menuId = 0;

    /**
     * @deprecated since ACP3 version 6.6.0. Will be removed with version 7.0.0. Use ::withMenuId instead.
     */
    public function setMenuId(int $menuId): static
    {
        $this->menuId = $menuId;

        return $this;
    }

    public function withMenuId(int $menuId): static
    {
        $clone = clone $this;
        $clone->menuId = $menuId;

        return $clone;
    }

    public function validate(array $formData): void
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::class)
            ->addConstraint(
                MenuNameValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'index_name',
                    'message' => $this->translator->t('menus', 'type_in_index_name'),
                ]
            )
            ->addConstraint(
                MenuAlreadyExistsValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'index_name',
                    'message' => $this->translator->t('menus', 'index_name_unique'),
                    'extra' => [
                        'menu_id' => $this->menuId,
                    ],
                ]
            )
            ->addConstraint(
                Core\Validation\ValidationRules\NotEmptyValidationRule::class,
                [
                    'data' => $formData,
                    'field' => 'title',
                    'message' => $this->translator->t('menus', 'menu_bar_title_to_short'),
                ]
            );

        $this->validator->validate();
    }
}
