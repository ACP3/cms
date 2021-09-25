<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Search\Installer;

use ACP3\Core\ACL\PrivilegeEnum;

class Schema implements \ACP3\Core\Installer\SchemaInterface
{
    public const MODULE_NAME = 'search';

    /**
     * {@inheritDoc}
     */
    public function specialResources(): array
    {
        return [
            'frontend' => [
                'index' => [
                    'index' => PrivilegeEnum::FRONTEND_VIEW,
                ],
            ],
            'widget' => [
                'index' => [
                    'index' => PrivilegeEnum::FRONTEND_VIEW,
                ],
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getModuleName(): string
    {
        return static::MODULE_NAME;
    }

    /**
     * {@inheritDoc}
     */
    public function createTables(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function removeTables(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function settings(): array
    {
        return [];
    }
}
