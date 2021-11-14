<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Menus\Repository\MenuItemRepository;

class AllowedMenuValidationRule extends AbstractValidationRule
{
    public function __construct(private MenuItemRepository $menuItemRepository)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (\is_array($data) && \is_array($field)) {
            $parentId = reset($field);
            $blockId = next($field);

            return $this->checkIsAllowedMenu($data[$parentId], $data[$blockId]);
        }

        return false;
    }

    protected function checkIsAllowedMenu(int $parentId, int $menuId): bool
    {
        if (empty($parentId)) {
            return true;
        }

        $parentMenuId = $this->menuItemRepository->getMenuIdByMenuItemId($parentId);

        return !empty($parentMenuId) && $parentMenuId === $menuId;
    }
}
