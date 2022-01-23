<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DataGrid\Repository;

use ACP3\Core\DataGrid\ColumnPriorityQueue;
use ACP3\Core\DataGrid\QueryOption;
use ACP3\Core\Repository\AbstractRepository;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;

abstract class AbstractDataGridRepository extends AbstractRepository
{
    /**
     * @return array<string, mixed>[]
     *
     * @throws Exception
     */
    public function getAll(ColumnPriorityQueue $columns, QueryOption ...$queryOptions): array
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

        return $queryBuilder->executeQuery()->fetchAllAssociative();
    }

    /**
     * @throws Exception
     */
    public function countAll(QueryOption ...$queryOptions): int
    {
        $queryBuilder = $this->db->getConnection()->createQueryBuilder();
        $queryBuilder
            ->select('COUNT(*)')
            ->from($this->getTableName(), 'main')
            ->setParameters($this->getParameters(...$queryOptions));

        $this->addJoin($queryBuilder);
        $this->addWhere($queryBuilder, ...$queryOptions);

        return (int) $queryBuilder->executeQuery()->fetchOne();
    }

    /**
     * @return string[]
     */
    protected function getColumns(ColumnPriorityQueue $gridColumns): array
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

    protected function getFrom(): string
    {
        return $this->getTableName();
    }

    protected function addJoin(QueryBuilder $queryBuilder): void
    {
    }

    protected function addWhere(QueryBuilder $queryBuilder, QueryOption ...$queryOptions): void
    {
        foreach ($queryOptions as $option) {
            $queryBuilder->where(
                "`{$option->getTableAlias()}`.`{$option->getColumnName()}` {$option->getOperator()} :{$option->getColumnName()}"
            );
        }
    }

    protected function addGroupBy(QueryBuilder $queryBuilder): void
    {
    }

    protected function setOrderBy(ColumnPriorityQueue $gridColumns, QueryBuilder $queryBuilder): void
    {
        foreach (clone $gridColumns as $gridColumn) {
            if ($gridColumn['default_sort'] === true) {
                if (!\is_array($gridColumn['fields'])) {
                    $gridColumn['fields'] = [$gridColumn['fields']];
                }

                foreach ($gridColumn['fields'] as $field) {
                    $queryBuilder->addOrderBy($field, strtoupper($gridColumn['default_sort_direction']));
                }
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function getParameters(QueryOption ...$queryOptions): array
    {
        $bindings = [];
        foreach ($queryOptions as $option) {
            $bindings[$option->getColumnName()] = $option->getValue();
        }

        return $bindings;
    }
}
