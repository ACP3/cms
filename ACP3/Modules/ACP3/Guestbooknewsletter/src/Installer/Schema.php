<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbooknewsletter\Installer;

class Schema implements \ACP3\Core\Installer\SchemaInterface
{
    public const MODULE_NAME = 'guestbooknewsletter';

    public function specialResources(): array
    {
        return [];
    }

    public function getModuleName(): string
    {
        return static::MODULE_NAME;
    }

    public function createTables(): array
    {
        return [];
    }

    public function removeTables(): array
    {
        return [];
    }

    public function settings(): array
    {
        return [
            'newsletter_integration' => 0,
        ];
    }
}
