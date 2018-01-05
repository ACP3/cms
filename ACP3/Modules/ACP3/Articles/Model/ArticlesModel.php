<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Model;

use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Core\Model\DuplicationAwareTrait;
use ACP3\Modules\ACP3\Articles\Installer\Schema;

class ArticlesModel extends AbstractModel
{
    use DuplicationAwareTrait;

    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * @inheritdoc
     */
    public function save(array $rawData, $entryId = null)
    {
        $rawData['updated_at'] = 'now';

        return parent::save($rawData, $entryId);
    }

    /**
     * @return array
     */
    protected function getDefaultDataForDuplication()
    {
        return [
            'active' => 0,
            'start' => 'now',
            'end' => 'now',
        ];
    }

    /**
     * @return array
     */
    protected function getAllowedColumns()
    {
        return [
            'active' => DataProcessor\ColumnTypes::COLUMN_TYPE_BOOLEAN,
            'start' => DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
            'end' => DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
            'updated_at' => DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
            'title' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT,
            'text' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT_WYSIWYG,
            'user_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
        ];
    }
}
