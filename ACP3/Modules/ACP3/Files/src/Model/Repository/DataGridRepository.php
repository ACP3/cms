<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Model\Repository;

use ACP3\Core\DataGrid\ColumnPriorityQueue;
use ACP3\Core\DataGrid\Model\Repository\AbstractDataGridRepository;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository;
use Doctrine\DBAL\Query\QueryBuilder;

class DataGridRepository extends AbstractDataGridRepository
{
    public const TABLE_NAME = FilesRepository::TABLE_NAME;

    /**
     * {@inheritdoc}
     */
    protected function getColumns(ColumnPriorityQueue $gridColumns)
    {
        return [
            'main.*',
            'c.title AS cat',
            "(SELECT MIN(fmin.`sort`) FROM {$this->getTableName()} fmin WHERE fmin.category_id = main.category_id) AS `first`",
            "(SELECT MAX(fmax.`sort`) FROM {$this->getTableName()} fmax WHERE fmax.category_id = main.category_id) AS `last`",
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function addJoin(QueryBuilder $queryBuilder)
    {
        $queryBuilder->leftJoin(
            'main',
            $this->getTableName(CategoryRepository::TABLE_NAME),
            'c',
            'main.category_id = c.id'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function setOrderBy(ColumnPriorityQueue $gridColumns, QueryBuilder $queryBuilder)
    {
        $queryBuilder
            ->addOrderBy('main.start', 'DESC')
            ->addOrderBy('main.end', 'DESC')
            ->addOrderBy('main.id', 'DESC');
    }
}
