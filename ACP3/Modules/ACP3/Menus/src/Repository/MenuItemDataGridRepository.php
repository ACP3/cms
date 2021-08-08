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
                if ($nestedSet[$j]['parent_id'] == $nestedSet[$index]['parent_id']) {
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
            if ($nestedSet[$index]['parent_id'] == $nestedSet[$j]['parent_id']) {
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
            'r.*',
            'COUNT(*) - 1 AS level',
            "CONCAT(REPEAT('&nbsp;&nbsp;', COUNT(*) - 1), r.title) AS title_nested",
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function addJoin(QueryBuilder $queryBuilder)
    {
        $queryBuilder->join('main', $this->getTableName(), 'r');
    }

    protected function addWhere(QueryBuilder $queryBuilder, QueryOption ...$queryOptions)
    {
        parent::addWhere($queryBuilder, ...$queryOptions);

        $queryBuilder->andWhere('r.left_id BETWEEN main.left_id AND main.right_id');
    }

    protected function addGroupBy(QueryBuilder $queryBuilder)
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
    protected function setOrderBy(ColumnPriorityQueue $gridColumns, QueryBuilder $queryBuilder)
    {
        $queryBuilder->addOrderBy('r.left_id', 'ASC');
    }
}
