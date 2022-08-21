<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Repository;

use ACP3\Core\DataGrid\ColumnPriorityQueue;
use ACP3\Core\DataGrid\QueryOption;
use ACP3\Core\DataGrid\Repository\AbstractDataGridRepository;
use ACP3\Modules\ACP3\System\Repository\ModulesRepository;
use Doctrine\DBAL\Query\QueryBuilder;

class DataGridRepository extends AbstractDataGridRepository
{
    public const TABLE_NAME = CategoryRepository::TABLE_NAME;

    /**
     * {@inheritDoc}
     */
    public function getAll(ColumnPriorityQueue $columns, QueryOption ...$queryOptions): array
    {
        return parent::getAll($columns, ...$queryOptions);
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns(ColumnPriorityQueue $gridColumns): array
    {
        return [
            'c.*',
            'COUNT(*) - 1 AS level',
            "CONCAT(REPEAT('&nbsp;&nbsp;', COUNT(*) - 1), c.title) AS title_nested",
            'm.name AS module',
            "(SELECT IF(COUNT(id) = 0, 1, 0) FROM {$this->getTableName(self::TABLE_NAME)} f WHERE f.module_id = c.module_id AND f.parent_id = c.parent_id AND f.left_id < c.left_id) AS first",
            "(SELECT IF(COUNT(id) = 0, 1, 0) FROM {$this->getTableName(self::TABLE_NAME)} l WHERE l.module_id = c.module_id AND l.parent_id = c.parent_id AND l.left_id > c.left_id) AS last",
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function addJoin(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->join('main', $this->getTableName(), 'c');
        $queryBuilder->leftJoin(
            'main',
            $this->getTableName(ModulesRepository::TABLE_NAME),
            'm',
            'main.module_id = m.id'
        );
    }

    protected function addWhere(QueryBuilder $queryBuilder, QueryOption ...$queryOptions): void
    {
        $queryBuilder->where('c.left_id BETWEEN main.left_id AND main.right_id');
    }

    protected function addGroupBy(QueryBuilder $queryBuilder): void
    {
        $queryBuilder
            ->addGroupBy('c.left_id')
            ->addGroupBy('c.id')
            ->addGroupBy('c.root_id')
            ->addGroupBy('c.parent_id')
            ->addGroupBy('c.right_id')
            ->addGroupBy('c.title')
            ->addGroupBy('c.picture')
            ->addGroupBy('c.description')
            ->addGroupBy('c.module_id')
            ->addGroupBy('m.name');
    }

    /**
     * {@inheritdoc}
     */
    protected function setOrderBy(ColumnPriorityQueue $gridColumns, QueryBuilder $queryBuilder): void
    {
        $queryBuilder
            ->addOrderBy('module', 'ASC')
            ->addOrderBy('c.left_id', 'ASC');
    }
}
