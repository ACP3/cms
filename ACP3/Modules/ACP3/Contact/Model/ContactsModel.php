<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Contact\Model;

use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor\ColumnTypes;

class ContactsModel extends AbstractModel
{
    /**
     * @inheritdoc
     */
    public function save(array $rawData, $entryId = null)
    {
        $rawData['date'] = 'now';

        return parent::save($rawData, $entryId);
    }

    /**
     * @inheritdoc
     */
    protected function getAllowedColumns()
    {
        return [
            'date' => ColumnTypes::COLUMN_TYPE_DATETIME,
            'name' => ColumnTypes::COLUMN_TYPE_TEXT,
            'mail' => ColumnTypes::COLUMN_TYPE_TEXT,
            'message' => ColumnTypes::COLUMN_TYPE_TEXT,
        ];
    }
}
