<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Repository;

use ACP3\Core\DataGrid\ColumnPriorityQueue;
use ACP3\Core\DataGrid\QueryOption;
use ACP3\Core\DataGrid\Repository\AbstractDataGridRepository;
use Doctrine\DBAL\Query\QueryBuilder;

class MenuItemDataGridRepository extends AbstractDataGridRepository
{
    public const TABLE_NAME = MenuItemRepository::TABLE_NAME;

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
            'r.*',
            'COUNT(*) - 1 AS level',
            "CONCAT(REPEAT('&nbsp;&nbsp;', COUNT(*) - 1), r.title) AS title_nested",
            "(SELECT IF(COUNT(id) = 0, 1, 0) FROM {$this->getTableName(self::TABLE_NAME)} f WHERE f.block_id = r.block_id AND f.parent_id = r.parent_id AND f.left_id < r.left_id) AS first",
            "(SELECT IF(COUNT(id) = 0, 1, 0) FROM {$this->getTableName(self::TABLE_NAME)} l WHERE l.block_id = r.block_id AND l.parent_id = r.parent_id AND l.left_id > r.left_id) AS last",
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function addJoin(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->join('main', $this->getTableName(), 'r');
    }

    protected function addWhere(QueryBuilder $queryBuilder, QueryOption ...$queryOptions): void
    {
        parent::addWhere($queryBuilder, ...$queryOptions);

        $queryBuilder->andWhere('r.left_id BETWEEN main.left_id AND main.right_id');
    }

    protected function addGroupBy(QueryBuilder $queryBuilder): void
    {
        $queryBuilder
            ->addGroupBy('r.left_id')
            ->addGroupBy('r.id')
            ->addGroupBy('r.title')
            ->addGroupBy('r.mode')
            ->addGroupBy('r.display')
            ->addGroupBy('r.uri')
            ->addGroupBy('r.target')
            ->addGroupBy('r.block_id')
            ->addGroupBy('r.root_id')
            ->addGroupBy('r.parent_id')
            ->addGroupBy('r.right_id')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function setOrderBy(ColumnPriorityQueue $gridColumns, QueryBuilder $queryBuilder): void
    {
        $queryBuilder->addOrderBy('r.left_id', 'ASC');
    }
}
