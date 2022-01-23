<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Services;

interface MenuServiceInterface
{
    /**
     * @return array<array<string, mixed>>
     */
    public function getAllMenuItems(): array;

    /**
     * @return array<array<string, mixed>>
     */
    public function getVisibleMenuItemsByMenu(string $menuIdentifier): array;
}
