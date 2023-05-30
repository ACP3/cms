<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Menus\Repository\MenuItemRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AllowedMenuValidationRule extends AbstractValidationRule
{
    public function __construct(private readonly MenuItemRepository $menuItemRepository)
    {
    }

    public function isValid(bool|int|float|string|array|UploadedFile|null $data, string|array $field = '', array $extra = []): bool
    {
        if (\is_array($data) && \is_array($field)) {
            $parentId = reset($field);
            $blockId = next($field);

            return $this->checkIsAllowedMenu((int) $data[$parentId], (int) $data[$blockId]);
        }

        return false;
    }

    private function checkIsAllowedMenu(int $parentId, int $menuId): bool
    {
        if (empty($parentId)) {
            return true;
        }

        $parentMenuId = $this->menuItemRepository->getMenuIdByMenuItemId($parentId);

        return !empty($parentMenuId) && $parentMenuId === $menuId;
    }
}
