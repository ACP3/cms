<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Model;

use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor\ColumnType\RawColumnType;
use ACP3\Core\Model\DataProcessor\ColumnType\TextColumnType;
use ACP3\Modules\ACP3\Menus\Installer\Schema;

class MenusModel extends AbstractModel
{
    public const EVENT_PREFIX = Schema::MODULE_NAME;

    protected function getAllowedColumns(): array
    {
        return [
            'index_name' => RawColumnType::class,
            'title' => TextColumnType::class,
        ];
    }
}
