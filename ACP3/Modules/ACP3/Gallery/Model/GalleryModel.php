<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Model;

use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Core\Model\UpdatedAtAwareModelInterface;
use ACP3\Modules\ACP3\Gallery\Installer\Schema;

class GalleryModel extends AbstractModel implements UpdatedAtAwareModelInterface
{
    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * {@inheritdoc}
     */
    public function save(array $rawData, $entryId = null)
    {
        $rawData['updated_at'] = 'now';

        return parent::save($rawData, $entryId);
    }

    /**
     * @return array
     */
    protected function getAllowedColumns()
    {
        return [
            'start' => DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
            'end' => DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
            'updated_at' => DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
            'title' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT,
            'user_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
        ];
    }
}
