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
     * @param array<string, mixed> $updatedData
     *
     * @throws InvalidFormTokenException
     * @throws ValidationFailedException
     * @throws ValidationRuleNotFoundException
     * @throws Exception
     */
    public function upsert(array $updatedData, ?int $menuItemId = null): int
    {
        $this->menuItemFormValidation->validate($updatedData);

        $updatedData['uri'] = $this->fetchMenuItemUriForSave($updatedData);

        return $this->menuItemsModel->save($updatedData, $menuItemId);
    }

    /**
     * @param array<string, mixed> $menuItemData
     */
    private function fetchMenuItemUriForSave(array $menuItemData): string
    {
        if (PageTypeEnum::MODULE->value === (int) $menuItemData['mode']) {
            return $menuItemData['module'];
        }

        return $menuItemData['uri'];
    }
}
