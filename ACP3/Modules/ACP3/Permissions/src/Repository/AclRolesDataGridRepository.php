<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Repository;

use ACP3\Core\DataGrid\ColumnPriorityQueue;
use ACP3\Core\DataGrid\QueryOption;
use ACP3\Core\DataGrid\Repository\AbstractDataGridRepository;
use Doctrine\DBAL\Query\QueryBuilder;

class AclRolesDataGridRepository extends AbstractDataGridRepository
{
    public const TABLE_NAME = AclRoleRepository::TABLE_NAME;

    public function getAll(ColumnPriorityQueue $columns, QueryOption ...$queryOptions): array
    {
        return parent::getAll($columns, ...$queryOptions);
    }

    protected function getColumns(ColumnPriorityQueue $gridColumns): array
    {
        return [
            'r.*',
            'COUNT(*) - 1 AS level',
            "CONCAT(REPEAT('&nbsp;&nbsp;', COUNT(*) - 1), r.name) AS name_nested",
            "(SELECT IF(COUNT(id) = 0, 1, 0) FROM {$this->getTableName(self::TABLE_NAME)} f WHERE f.parent_id = r.parent_id AND f.left_id < r.left_id) AS first",
            "(SELECT IF(COUNT(id) = 0, 1, 0) FROM {$this->getTableName(self::TABLE_NAME)} l WHERE l.parent_id = r.parent_id AND l.left_id > r.left_id) AS last",
        ];
    }

    protected function addJoin(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->join('main', $this->getTableName(), 'r');
    }

    protected function addWhere(QueryBuilder $queryBuilder, QueryOption ...$queryOptions): void
    {
        $queryBuilder->where('r.left_id BETWEEN main.left_id AND main.right_id');
    }

    protected function addGroupBy(QueryBuilder $queryBuilder): void
    {
        $queryBuilder
            ->addGroupBy('r.left_id')
            ->addGroupBy('r.id')
            ->addGroupBy('r.name')
            ->addGroupBy('r.root_id')
            ->addGroupBy('r.parent_id')
            ->addGroupBy('r.right_id')
        ;
    }

    protected function setOrderBy(ColumnPriorityQueue $gridColumns, QueryBuilder $queryBuilder): void
    {
        $queryBuilder->addOrderBy('r.left_id', 'ASC');
    }
}
