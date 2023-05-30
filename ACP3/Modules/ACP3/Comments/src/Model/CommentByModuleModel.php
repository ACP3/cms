<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Model;

use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor\ColumnType\DateTimeColumnType;
use ACP3\Core\Model\DataProcessor\ColumnType\IntegerColumnType;
use ACP3\Core\Model\DataProcessor\ColumnType\IntegerNullableColumnType;
use ACP3\Core\Model\DataProcessor\ColumnType\RawColumnType;
use ACP3\Core\Model\DataProcessor\ColumnType\TextColumnType;
use ACP3\Modules\ACP3\Comments\Installer\Schema;

/**
 * @property \ACP3\Modules\ACP3\Comments\Repository\CommentByModuleRepository $repository
 */
class CommentByModuleModel extends AbstractModel
{
    public const EVENT_PREFIX = Schema::MODULE_NAME;

    protected function getAllowedColumns(): array
    {
        return [
            'ip' => RawColumnType::class,
            'date' => DateTimeColumnType::class,
            'name' => TextColumnType::class,
            'user_id' => IntegerNullableColumnType::class,
            'message' => TextColumnType::class,
            'module_id' => IntegerColumnType::class,
            'entry_id' => IntegerColumnType::class,
        ];
    }
}
