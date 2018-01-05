<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Categories\Model;

use ACP3\Core\Model\AbstractNestedSetModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Modules\ACP3\Categories\Installer\Schema;

class CategoriesModel extends AbstractNestedSetModel
{
    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * @inheritdoc
     */
    public function save(array $data, $entryId = null)
    {
        if (isset($data['module'])) {
            $data['module_id'] = $data['module'];
        }

        return parent::save($data, $entryId);
    }

    /**
     * @return array
     */
    protected function getAllowedColumns()
    {
        return [
            'parent_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
            'title' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT,
            'description' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT,
            'module_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
            'picture' => DataProcessor\ColumnTypes::COLUMN_TYPE_RAW,
        ];
    }
}
