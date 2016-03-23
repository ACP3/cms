<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Categories\Model;


use ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue;
use ACP3\Modules\ACP3\System\Model\ModuleRepository;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Class DataGridRepository
 * @package ACP3\Modules\ACP3\Categories\Model
 */
class DataGridRepository extends \ACP3\Core\Model\DataGridRepository
{
    const TABLE_NAME = CategoryRepository::TABLE_NAME;

    /**
     * @inheritdoc
     */
    protected function getColumns(ColumnPriorityQueue $gridColumns)
    {
        return [
            'main.*',
            'm.name AS module'
        ];
    }

    /**
     * @inheritdoc
     */
    protected function addJoin(QueryBuilder $queryBuilder)
    {
        $queryBuilder->leftJoin(
            'main',
            $this->getTableName(ModuleRepository::TABLE_NAME),
            'm',
            'main.module_id = m.id'
        );
    }

    /**
     * @inheritdoc
     */
    protected function setOrderBy(ColumnPriorityQueue $gridColumns, QueryBuilder $queryBuilder)
    {
        $queryBuilder
            ->addOrderBy('module', 'ASC')
            ->addOrderBy('main.title', 'DESC')
            ->addOrderBy('main.id', 'DESC');
    }
}
