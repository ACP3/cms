<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Auditlog\Model\Repository;

use ACP3\Core\DataGrid\ColumnPriorityQueue;
use ACP3\Core\DataGrid\Model\Repository\AbstractDataGridRepository;
use ACP3\Modules\ACP3\System\Model\Repository\ModulesRepository;
use Doctrine\DBAL\Query\QueryBuilder;

class AuditLogDataGridRepository extends AbstractDataGridRepository
{
    public const TABLE_NAME = AuditLogRepository::TABLE_NAME;

    protected function getColumns(ColumnPriorityQueue $gridColumns)
    {
        return [
            'main.module_id',
            'm.name AS module_name',
            'main.table_name',
            'COUNT(distinct main.entry_id) AS results_count',
        ];
    }

    protected function addJoin(QueryBuilder $queryBuilder)
    {
        $queryBuilder->join('main', $this->getTableName(ModulesRepository::TABLE_NAME), 'm', 'm.id = main.module_id');
    }

    protected function addGroupBy(QueryBuilder $queryBuilder)
    {
        $queryBuilder->addGroupBy(['main.table_name', 'main.module_id', 'm.name']);
    }
}
