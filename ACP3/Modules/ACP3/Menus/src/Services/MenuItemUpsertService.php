<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Services;

use ACP3\Core\Validation\Exceptions\InvalidFormTokenException;
use ACP3\Core\Validation\Exceptions\ValidationFailedException;
use ACP3\Core\Validation\Exceptions\ValidationRuleNotFoundException;
use ACP3\Modules\ACP3\Menus\Enum\PageTypeEnum;
use ACP3\Modules\ACP3\Menus\Model\MenuItemsModel;
use ACP3\Modules\ACP3\Menus\Validation\MenuItemFormValidation;
use Doctrine\DBAL\Exception;

class MenuItemUpsertService
{
    public function __construct(private readonly MenuItemFormValidation $menuItemFormValidation, private readonly MenuItemsModel $menuItemsModel)
    {
    }

    /**
     * @param array<string, mixed> $menuItem
     *
     * @throws InvalidFormTokenException
     * @throws ValidationFailedException
     * @throws ValidationRuleNotFoundException
     * @throws Exception
     */
    public function upsert(array $menuItem, ?int $menuItemId = null): int
    {
        $this->menuItemFormValidation->validate($menuItem);

        $menuItem['uri'] = $this->fetchMenuItemUriForSave($menuItem);

        return $this->menuItemsModel->save($menuItem, $menuItemId);
    }

    /**
     * @param array<string, mixed> $menuItem
     */
    private function fetchMenuItemUriForSave(array $menuItem): string
    {
        if (PageTypeEnum::MODULE->value === (int) $menuItem['mode']) {
            return $menuItem['module'];
        }

        return $menuItem['uri'];
    }
}
