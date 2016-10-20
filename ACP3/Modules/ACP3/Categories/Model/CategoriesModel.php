<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Categories\Model;


use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Modules\ACP3\Categories\Installer\Schema;

class CategoriesModel extends AbstractModel
{
    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * @param array $data
     * @param int|null $entryId
     * @return bool|int
     */
    public function saveCategory(array $data, $entryId = null)
    {
        if (isset($data['module'])) {
            $data['module_id'] = $data['module'];
        }

        return $this->save($data, $entryId);
    }

    /**
     * @return array
     */
    protected function getAllowedColumns()
    {
        return [
            'title' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT,
            'description' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT,
            'module_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
            'picture' => DataProcessor\ColumnTypes::COLUMN_TYPE_RAW
        ];
    }
}
