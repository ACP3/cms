<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Model;


use ACP3\Core\Model\AbstractNestedSetModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Modules\ACP3\Permissions\Installer\Schema;

class RolesModel extends AbstractNestedSetModel
{
    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * @return array
     */
    protected function getAllowedColumns()
    {
        return [
            'name' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT,
            'parent_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT
        ];
    }
}
