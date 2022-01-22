<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Model;

use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Modules\ACP3\Emoticons\Installer\Schema;

class EmoticonsModel extends AbstractModel
{
    public const EVENT_PREFIX = Schema::MODULE_NAME;

    protected function getAllowedColumns(): array
    {
        return [
            'code' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT,
            'description' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT,
            'img' => DataProcessor\ColumnTypes::COLUMN_TYPE_RAW,
        ];
    }
}
