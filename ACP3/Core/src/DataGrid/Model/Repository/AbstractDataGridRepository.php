<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid\Model\Repository;

use ACP3\Core\DataGrid\ColumnPriorityQueue;
use ACP3\Core\DataGrid\QueryOption;
use ACP3\Core\Model\Repository\AbstractRepository;
use Doctrine\DBAL\Query\QueryBuilder;

abstract class AbstractDataGridRepository extends AbstractRepository
{
    /**
     * @param \ACP3\Core\DataGrid\QueryOption ...$queryOptions
     *
     * @return array
     */
    public function getAll(ColumnPriorityQueue $columns, QueryOption ...$queryOptions)
    {
        $queryBuilder = $this->db->getConnection()->createQueryBuilder();
        $queryBuilder
            ->select($this->getColumns($columns))
            ->from($this->getTableName(), 'main')
            ->setParameters($this->getParameters(...$queryOptions));

        $this->addJoin($queryBuilder);
        $this->addWhere($queryBuilder, ...$queryOptions);
        $this->addGroupBy($queryBuilder);
        $this->setOrderBy($columns, $queryBuilder);

        return $queryBuilder->execute()->fetchAll();
    }

    /**
     * @param \ACP3\Core\DataGrid\QueryOption ...$queryOptions
     *
     * @return int
     */
    public function countAll(QueryOption ...$queryOptions)
    {
        $queryBuilder = $this->db->getConnection()->createQueryBuilder();
        $queryBuilder
            ->select('COUNT(*)')
            ->from($this->getTableName(), 'main')
            ->setParameters($this->getParameters(...$queryOptions));

        $this->addJoin($queryBuilder);
        $this->addWhere($queryBuilder, ...$queryOptions);

        return (int) $queryBuilder->execute()->fetchColumn();
    }

    /**
     * @return array
     */
    protected function getColumns(ColumnPriorityQueue $gridColumns)
    {
        $columnsToSelect = [];
        foreach (clone $gridColumns as $column) {
            if (!empty($column['fields'])) {
                if (!\is_array($column['fields'])) {
                    $column['fields'] = [$column['fields']];
                }

                foreach ($column['fields'] as $field) {
                    $columnsToSelect[] = $field;
                }
            }
        }

        return $columnsToSelect;
    }

    /**
     * @return string
     */
    protected function getFrom()
    {
        return $this->getTableName();
    }

    protected function addJoin(QueryBuilder $queryBuilder)
    {
    }

    /**
     * @param \ACP3\Core\DataGrid\QueryOption ...$queryOptions
     */
    protected function addWhere(QueryBuilder $queryBuilder, QueryOption ...$queryOptions)
    {
        foreach ($queryOptions as $option) {
            $queryBuilder->where(
                "`{$option->getTableAlias()}`.`{$option->getColumnName()}` {$option->getOperator()} :{$option->getColumnName()}"
            );
        }
    }

    protected function addGroupBy(QueryBuilder $queryBuilder)
    {
    }

    protected function setOrderBy(ColumnPriorityQueue $gridColumns, QueryBuilder $queryBuilder)
    {
        foreach (clone $gridColumns as $gridColumn) {
            if ($gridColumn['default_sort'] === true) {
                if (!\is_array($gridColumn['fields'])) {
                    $gridColumn['fields'] = [$gridColumn['fields']];
                }

                foreach ($gridColumn['fields'] as $field) {
                    $queryBuilder->addOrderBy($field, \strtoupper($gridColumn['default_sort_direction']));
                }
            }
        }
    }

    /**
     * @param \ACP3\Core\DataGrid\QueryOption ...$queryOptions
     *
     * @return array
     */
    protected function getParameters(QueryOption ...$queryOptions)
    {
        $bindings = [];
        foreach ($queryOptions as $option) {
            $bindings[$option->getColumnName()] = $option->getValue();
        }

        return $bindings;
    }
}
