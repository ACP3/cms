<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Categories\Model\Repository;

use ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue;
use ACP3\Core\Helpers\DataGrid\Model\Repository\AbstractDataGridRepository;
use ACP3\Modules\ACP3\System\Model\Repository\ModulesRepository;
use Doctrine\DBAL\Query\QueryBuilder;

class CategoriesDataGridRepository extends AbstractDataGridRepository
{
    const TABLE_NAME = CategoriesRepository::TABLE_NAME;

    /**
     * @inheritdoc
     */
    protected function getColumns(ColumnPriorityQueue $gridColumns)
    {
        return [
            'main.*',
            'm.name AS module',
            '(SELECT MIN(pmin.left_id) FROM ' . $this->getTableName() . ' AS pmin WHERE pmin.module_id = main.module_id) AS `first`',
            '(SELECT MAX(pmax.left_id) FROM ' . $this->getTableName() . ' AS pmax WHERE pmax.module_id = main.module_id) AS `last`'
        ];
    }

    /**
     * @inheritdoc
     */
    protected function addJoin(QueryBuilder $queryBuilder)
    {
        $queryBuilder->leftJoin(
            'main',
            $this->getTableName(ModulesRepository::TABLE_NAME),
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
            ->addOrderBy('main.left_id', 'ASC');
    }
}
