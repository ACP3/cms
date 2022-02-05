<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Model;

use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor\ColumnType\DateTimeColumnType;
use ACP3\Core\Model\DataProcessor\ColumnType\IntegerColumnType;
use ACP3\Core\Model\DataProcessor\ColumnType\IntegerNullableColumnType;
use ACP3\Core\Model\DataProcessor\ColumnType\RawColumnType;
use ACP3\Core\Model\DataProcessor\ColumnType\TextColumnType;
use ACP3\Modules\ACP3\Guestbook\Installer\Schema;

class GuestbookModel extends AbstractModel
{
    public const EVENT_PREFIX = Schema::MODULE_NAME;

    protected function getAllowedColumns(): array
    {
        return [
            'date' => DateTimeColumnType::class,
            'ip' => RawColumnType::class,
            'name' => TextColumnType::class,
            'user_id' => IntegerNullableColumnType::class,
            'message' => TextColumnType::class,
            'website' => TextColumnType::class,
            'mail' => RawColumnType::class,
            'active' => IntegerColumnType::class,
        ];
    }
}
