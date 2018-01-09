<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Model;

use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Modules\ACP3\Permissions\Installer\Schema;

class AclResourcesModel extends AbstractModel
{
    const EVENT_PREFIX = Schema::MODULE_NAME;

    /**
     * {@inheritdoc}
     */
    public function save(array $rawData, ?int $entryId = null): int
    {
        $rawData = \array_merge($rawData, [
            'page' => $rawData['resource'],
            'privilege_id' => $rawData['privileges'],
        ]);

        return parent::save($rawData, $entryId);
    }

    /**
     * @return array
     */
    protected function getAllowedColumns()
    {
        return [
            'module_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
            'area' => DataProcessor\ColumnTypes::COLUMN_TYPE_RAW,
            'controller' => DataProcessor\ColumnTypes::COLUMN_TYPE_RAW,
            'page' => DataProcessor\ColumnTypes::COLUMN_TYPE_RAW,
            'privilege_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
        ];
    }
}
