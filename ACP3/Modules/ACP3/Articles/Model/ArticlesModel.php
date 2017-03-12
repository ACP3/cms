<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Model;

use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Modules\ACP3\Articles\Installer\Schema;

class ArticlesModel extends AbstractModel
{
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
    protected function getAllowedColumns()
    {
        return [
            'start' => DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
            'end' => DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
            'updated_at' => DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
            'title' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT,
            'text' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT_WYSIWYG,
            'user_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT
        ];
    }
}
