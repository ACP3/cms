<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\Model;


use ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue;
use ACP3\Modules\ACP3\Categories\Model\CategoryRepository;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Class DataGridRepository
 * @package ACP3\Modules\ACP3\News\Model
 */
class DataGridRepository extends \ACP3\Core\Model\DataGridRepository
{
    const TABLE_NAME = NewsRepository::TABLE_NAME;

    /**
     * @inheritdoc
     */
    protected function getColumns(ColumnPriorityQueue $gridColumns)
    {
        return [
            'main.*',
            'c.title AS cat'
        ];
    }

    /**
     * @inheritdoc
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
