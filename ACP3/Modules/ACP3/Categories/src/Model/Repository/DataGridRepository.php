<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Model\Repository;

use ACP3\Core\DataGrid\ColumnPriorityQueue;
use ACP3\Core\DataGrid\Model\Repository\AbstractDataGridRepository;
use ACP3\Core\DataGrid\QueryOption;
use ACP3\Modules\ACP3\System\Model\Repository\ModulesRepository;
use Doctrine\DBAL\Query\QueryBuilder;

class DataGridRepository extends AbstractDataGridRepository
{
    public const TABLE_NAME = CategoryRepository::TABLE_NAME;

    public function getAll(ColumnPriorityQueue $columns, QueryOption ...$queryOptions)
    {
        $results = parent::getAll($columns, ...$queryOptions);

        return $this->calculateFirstAndLastPage($results);
    }

    /**
     * @return array
     */
    private function calculateFirstAndLastPage(array $results)
    {
        foreach ($results as $index => &$result) {
            $result['first'] = $this->isFirstInSet($index, $results);
            $result['last'] = $this->isLastItemInSet($index, $results);
        }

        return $results;
    }

    private function isFirstInSet(int $index, array $nestedSet): bool
    {
        if ($index > 0) {
            for ($j = $index - 1; $j >= 0; --$j) {
                if ($nestedSet[$j]['parent_id'] == $nestedSet[$index]['parent_id']
                    && $nestedSet[$j]['module_id'] == $nestedSet[$index]['module_id']
                ) {
                    return false;
                }
            }
        }

        return true;
    }

    private function isLastItemInSet(int $index, array $nestedSet): bool
    {
        $cItems = \count($nestedSet);
        for ($j = $index + 1; $j < $cItems; ++$j) {
            if ($nestedSet[$index]['parent_id'] == $nestedSet[$j]['parent_id']
                && $nestedSet[$j]['module_id'] == $nestedSet[$index]['module_id']
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns(ColumnPriorityQueue $gridColumns)
    {
        return [
            'c.*',
            'COUNT(*) - 1 AS level',
            "CONCAT(REPEAT('&nbsp;&nbsp;', COUNT(*) - 1), c.title) AS title_nested",
            'm.name AS module',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function addJoin(QueryBuilder $queryBuilder)
    {
        $queryBuilder->join('main', $this->getTableName(), 'c');
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
    protected function setOrderBy(ColumnPriorityQueue $gridColumns, QueryBuilder $queryBuilder)
    {
        $queryBuilder
            ->addOrderBy('module', 'ASC')
            ->addOrderBy('c.left_id', 'ASC');
    }
}
