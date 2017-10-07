<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Model\Repository;

use ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue;
use ACP3\Core\Helpers\DataGrid\Model\Repository\AbstractDataGridRepository;
use ACP3\Core\Helpers\DataGrid\QueryOption;
use Doctrine\DBAL\Query\QueryBuilder;

class AclRolesDataGridRepository extends AbstractDataGridRepository
{
    const TABLE_NAME = AclRolesRepository::TABLE_NAME;

    public function getAll(ColumnPriorityQueue $columns, QueryOption ...$queryOptions)
    {
        $results = parent::getAll($columns, ...$queryOptions);

        return $this->calculateFirstAndLastPage($results);
    }

    /**
     * @param array $results
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

    /**
     * @param int $index
     * @param array $nestedSet
     *
     * @return bool
     */
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

    /**
     * @param int $index
     * @param array $nestedSet
     *
     * @return bool
     */
    private function isLastItemInSet(int $index, array $nestedSet): bool
    {
        $cItems = count($nestedSet);
        for ($j = $index + 1; $j < $cItems; ++$j) {
            if ($nestedSet[$index]['parent_id'] == $nestedSet[$j]['parent_id']) {
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function getColumns(ColumnPriorityQueue $gridColumns)
    {
        return [
            'r.*',
            'COUNT(*) - 1 AS level',
            "CONCAT(REPEAT('&nbsp;&nbsp;', COUNT(*) - 1), r.name) AS name_nested",
        ];
    }

    /**
     * @inheritdoc
     */
    protected function addJoin(QueryBuilder $queryBuilder)
    {
        $queryBuilder->join('main', $this->getTableName(), 'r', true);
    }

    protected function addWhere(QueryBuilder $queryBuilder, QueryOption ...$queryOptions)
    {
        $queryBuilder->where('r.left_id BETWEEN main.left_id AND main.right_id');
    }

    protected function addGroupBy(QueryBuilder $queryBuilder)
    {
        $queryBuilder->addGroupBy('r.left_id');
    }

    /**
     * @inheritdoc
     */
    protected function setOrderBy(ColumnPriorityQueue $gridColumns, QueryBuilder $queryBuilder)
    {
        $queryBuilder->addOrderBy('r.left_id', 'ASC');
    }
}
