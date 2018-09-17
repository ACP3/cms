<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Model;

use ACP3\Core\NestedSet\Model\AbstractNestedSetModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Modules\ACP3\Menus\Installer\Schema;

class MenuItemsModel extends AbstractNestedSetModel
{
    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * {@inheritdoc}
     */
    public function save(array $data, $entryId = null)
    {
        $data['target'] = $data['display'] == 0 ? 1 : $data['target'];

        return parent::save($data, $entryId);
    }

    /**
     * @return array
     */
    protected function getAllowedColumns()
    {
        return [
            'mode' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
            'block_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
            'parent_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
            'display' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
            'title' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT,
            'uri' => DataProcessor\ColumnTypes::COLUMN_TYPE_RAW,
            'target' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
        ];
    }
}
