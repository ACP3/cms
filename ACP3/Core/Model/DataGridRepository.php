<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Model;

use ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue;

/**
 * Class DataGridRepository
 * @package ACP3\Core\Model
 */
class DataGridRepository extends AbstractRepository
{
    /**
     * @param \ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue $columns
     *
     * @return array
     */
    public function getAll(ColumnPriorityQueue $columns)
    {
        $columnsToSelect = $this->getColumns(clone $columns);
        $from = $this->getFrom();
        $orderBy = $this->getOrderBy(clone $columns);

        return $this->db->fetchAll(
            "SELECT {$columnsToSelect} FROM {$from}{$orderBy}",
            $this->getParameters()
        );
    }

    /**
     * @param \ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue $columns
     *
     * @return string
     */
    protected function getColumns(ColumnPriorityQueue $columns)
    {
        $columnsToSelect = [];
        foreach ($columns as $column) {
            if (!empty($column['fields'])) {
                $columnsToSelect[] = implode(', ', $column['fields']);
            }
        }

        return implode(', ', $columnsToSelect);
    }

    /**
     * @return string
     */
    protected function getFrom()
    {
        return $this->getTableName();
    }

    /**
     * @param \ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue $columns
     *
     * @return string
     */
    protected function getOrderBy(ColumnPriorityQueue $columns)
    {
        $orderBy = '';
        foreach ($columns as $column) {
            if ($column['default_sort'] === true) {
                $orderBy .= implode($column['default_sort_direction'] . ', ', $column['fields']) . ' ';
                $orderBy .= strtoupper($column['default_sort_direction']);
            }
        }

        return !empty($orderBy) ? ' ORDER BY ' . $orderBy : '';
    }

    /**
     * @return array
     */
    protected function getParameters()
    {
        return [];
    }
}
