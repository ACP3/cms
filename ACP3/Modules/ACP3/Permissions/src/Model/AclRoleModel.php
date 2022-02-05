<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Model;

use ACP3\Core\Model\DataProcessor\ColumnType\IntegerColumnType;
use ACP3\Core\Model\DataProcessor\ColumnType\TextColumnType;
use ACP3\Core\NestedSet\Model\AbstractNestedSetModel;
use ACP3\Modules\ACP3\Permissions\Installer\Schema;

class AclRoleModel extends AbstractNestedSetModel
{
    public const EVENT_PREFIX = Schema::MODULE_NAME;

    protected function getAllowedColumns(): array
    {
        return [
            'name' => TextColumnType::class,
            'parent_id' => IntegerColumnType::class,
        ];
    }
}
