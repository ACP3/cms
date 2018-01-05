<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Model;

use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor\ColumnTypes;
use ACP3\Modules\ACP3\Guestbook\Installer\Schema;

class GuestbookModel extends AbstractModel
{
    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * @return array
     */
    protected function getAllowedColumns()
    {
        return [
            'date' => ColumnTypes::COLUMN_TYPE_DATETIME,
            'ip' => ColumnTypes::COLUMN_TYPE_RAW,
            'name' => ColumnTypes::COLUMN_TYPE_TEXT,
            'user_id' => ColumnTypes::COLUMN_TYPE_INT_NULLABLE,
            'message' => ColumnTypes::COLUMN_TYPE_TEXT,
            'website' => ColumnTypes::COLUMN_TYPE_TEXT,
            'mail' => ColumnTypes::COLUMN_TYPE_RAW,
            'active' => ColumnTypes::COLUMN_TYPE_INT,
        ];
    }
}
