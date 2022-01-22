<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Model;

use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor\ColumnTypes;
use ACP3\Modules\ACP3\Comments\Installer\Schema;

/**
 * @property \ACP3\Modules\ACP3\Comments\Repository\CommentByModuleRepository $repository
 */
class CommentByModuleModel extends AbstractModel
{
    public const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * {@inheritDoc}
     */
    protected function getAllowedColumns(): array
    {
        return [
            'ip' => ColumnTypes::COLUMN_TYPE_RAW,
            'date' => ColumnTypes::COLUMN_TYPE_DATETIME,
            'name' => ColumnTypes::COLUMN_TYPE_TEXT,
            'user_id' => ColumnTypes::COLUMN_TYPE_INT_NULLABLE,
            'message' => ColumnTypes::COLUMN_TYPE_TEXT,
            'module_id' => ColumnTypes::COLUMN_TYPE_INT,
            'entry_id' => ColumnTypes::COLUMN_TYPE_INT,
        ];
    }
}
