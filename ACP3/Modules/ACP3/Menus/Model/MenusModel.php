<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Menus\Model;

use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Modules\ACP3\Menus\Installer\Schema;

class MenusModel extends AbstractModel
{
    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * @return array
     */
    protected function getAllowedColumns()
    {
        return [
            'index_name' => DataProcessor\ColumnTypes::COLUMN_TYPE_RAW,
            'title' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT
        ];
    }
}
