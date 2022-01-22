<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Model;

use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Modules\ACP3\Menus\Installer\Schema;

class MenusModel extends AbstractModel
{
    public const EVENT_PREFIX = Schema::MODULE_NAME;

    protected function getAllowedColumns(): array
    {
        return [
            'index_name' => DataProcessor\ColumnTypes::COLUMN_TYPE_RAW,
            'title' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT,
        ];
    }
}
