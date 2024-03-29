<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\Repository;

use ACP3\Core\DataGrid\ColumnPriorityQueue;
use ACP3\Core\DataGrid\Repository\AbstractDataGridRepository;
use ACP3\Modules\ACP3\Categories\Repository\CategoryRepository;
use Doctrine\DBAL\Query\QueryBuilder;

class DataGridRepository extends AbstractDataGridRepository
{
    public const TABLE_NAME = NewsRepository::TABLE_NAME;

    protected function getColumns(ColumnPriorityQueue $gridColumns): array
    {
        return [
            'main.*',
            'c.title AS cat',
        ];
    }

    protected function addJoin(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->leftJoin(
            'main',
            $this->getTableName(CategoryRepository::TABLE_NAME),
            'c',
            'main.category_id = c.id'
        );
    }

    protected function setOrderBy(ColumnPriorityQueue $gridColumns, QueryBuilder $queryBuilder): void
    {
        $queryBuilder
            ->addOrderBy('main.start', 'DESC')
            ->addOrderBy('main.end', 'DESC')
            ->addOrderBy('main.id', 'DESC');
    }
}
