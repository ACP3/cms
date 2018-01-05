<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\Model\Repository;

use ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue;
use ACP3\Core\Helpers\DataGrid\Model\Repository\AbstractDataGridRepository;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoriesRepository;
use Doctrine\DBAL\Query\QueryBuilder;

class FilesDataGridRepository extends AbstractDataGridRepository
{
    const TABLE_NAME = FilesRepository::TABLE_NAME;

    /**
     * @inheritdoc
     */
    protected function getColumns(ColumnPriorityQueue $gridColumns)
    {
        return [
            'main.*',
            'c.title AS cat',
            "(SELECT MIN(`sort`) FROM {$this->getTableName()}) AS `first`",
            "(SELECT MAX(`sort`) FROM {$this->getTableName()}) AS `last`",
        ];
    }

    /**
     * @inheritdoc
     */
    protected function addJoin(QueryBuilder $queryBuilder)
    {
        $queryBuilder->leftJoin(
            'main',
            $this->getTableName(CategoriesRepository::TABLE_NAME),
            'c',
            'main.category_id = c.id'
        );
    }

    /**
     * @inheritdoc
     */
    protected function setOrderBy(ColumnPriorityQueue $gridColumns, QueryBuilder $queryBuilder)
    {
        $queryBuilder
            ->addOrderBy('main.start', 'DESC')
            ->addOrderBy('main.end', 'DESC')
            ->addOrderBy('main.id', 'DESC');
    }
}
