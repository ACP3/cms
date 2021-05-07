<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Auditlog\Model\Repository;

use ACP3\Core\DataGrid\ColumnPriorityQueue;
use ACP3\Core\DataGrid\Model\Repository\AbstractDataGridRepository;
use Doctrine\DBAL\Query\QueryBuilder;

class AuditLogByTableDataGridRepository extends AbstractDataGridRepository
{
    public const TABLE_NAME = AuditLogRepository::TABLE_NAME;

    protected function getColumns(ColumnPriorityQueue $gridColumns)
    {
        return [
            'main.table_name',
            'main.entry_id',
            'COUNT(main.entry_id) AS versions_count',
            "(SELECT `action` FROM {$this->getTableName()} WHERE entry_id = main.entry_id ORDER BY id DESC LIMIT 1) AS last_action",
        ];
    }

    protected function addGroupBy(QueryBuilder $queryBuilder)
    {
        $queryBuilder->addGroupBy('main.table_name');
        $queryBuilder->addGroupBy('main.entry_id');
    }
}
