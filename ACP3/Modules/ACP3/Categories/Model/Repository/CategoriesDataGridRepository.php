<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Categories\Model\Repository;

use ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue;
use ACP3\Core\Helpers\DataGrid\Model\Repository\AbstractDataGridRepository;
use ACP3\Core\Helpers\DataGrid\QueryOption;
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
            'c.*',
            'COUNT(*) - 1 AS level',
            "CONCAT(REPEAT('&nbsp;&nbsp;', COUNT(*) - 1), c.title) AS title_nested",
            'm.name AS module',
            '(SELECT MIN(cmin.left_id) FROM ' . $this->getTableName() . ' AS cmin WHERE cmin.module_id = c.module_id) AS `first`',
            '(SELECT MAX(cmax.left_id) FROM ' . $this->getTableName() . ' AS cmax WHERE cmax.module_id = c.module_id) AS `last`'
        ];
    }

    /**
     * @inheritdoc
     */
    protected function addJoin(QueryBuilder $queryBuilder)
    {
        $queryBuilder->join('main', $this->getTableName(), 'c', true);
        $queryBuilder->leftJoin(
            'main',
            $this->getTableName(ModulesRepository::TABLE_NAME),
            'm',
            'main.module_id = m.id'
        );
    }

    protected function addWhere(QueryBuilder $queryBuilder, QueryOption ...$queryOptions)
    {
        $queryBuilder->where('c.left_id BETWEEN main.left_id AND main.right_id');
    }

    protected function addGroupBy(QueryBuilder $queryBuilder)
    {
        $queryBuilder->addGroupBy('c.left_id');
    }

    /**
     * @inheritdoc
     */
    protected function setOrderBy(ColumnPriorityQueue $gridColumns, QueryBuilder $queryBuilder)
    {
        $queryBuilder
            ->addOrderBy('module', 'ASC')
            ->addOrderBy('c.left_id', 'ASC');
    }
}
