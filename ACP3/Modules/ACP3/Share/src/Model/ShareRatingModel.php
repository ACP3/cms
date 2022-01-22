<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Model;

use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor\ColumnTypes;
use ACP3\Modules\ACP3\Share\Installer\Schema;

class ShareRatingModel extends AbstractModel
{
    public const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * {@inheritdoc}
     */
    protected function getAllowedColumns(): array
    {
        return [
            'stars' => ColumnTypes::COLUMN_TYPE_INT,
            'share_id' => ColumnTypes::COLUMN_TYPE_INT,
            'ip' => ColumnTypes::COLUMN_TYPE_RAW,
        ];
    }
}
