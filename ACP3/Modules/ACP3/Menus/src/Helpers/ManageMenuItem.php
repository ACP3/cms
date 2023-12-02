<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Helpers;

use ACP3\Core\Validation\Exceptions\InvalidFormTokenException;
use ACP3\Core\Validation\Exceptions\ValidationFailedException;
use ACP3\Core\Validation\Exceptions\ValidationRuleNotFoundException;
use ACP3\Modules\ACP3\Menus\Model\MenuItemsModel;
use ACP3\Modules\ACP3\Menus\Repository\MenuItemRepository;
use ACP3\Modules\ACP3\Menus\Services\MenuItemUpsertService;
use Doctrine\DBAL\Exception;

class ManageMenuItem
{
    public function __construct(private readonly MenuItemUpsertService $menuItemUpsertService, private readonly MenuItemsModel $menuItemsModel, private readonly MenuItemRepository $menuItemRepository)
    {
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws Exception
     * @throws InvalidFormTokenException
     * @throws ValidationFailedException
     * @throws ValidationRuleNotFoundException
     */
    public function manageMenuItem(string $path, array $data = []): bool
    {
        $menuItemId = $this->menuItemRepository->getMenuItemIdByUri($path);

        if (!empty($data)) {
            $data['uri'] = $path;
            $this->menuItemUpsertService->upsert($data, $menuItemId);

            return true;
        }

        if (!empty($menuItemId)) {
            return $this->menuItemsModel->delete($menuItemId) > 0;
        }

        return true;
    }
}
