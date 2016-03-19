<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Categories\Model;


use ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue;
use ACP3\Modules\ACP3\System\Model\ModuleRepository;

/**
 * Class DataGridRepository
 * @package ACP3\Modules\ACP3\Categories\Model
 */
class DataGridRepository extends \ACP3\Core\Model\DataGridRepository
{
    const TABLE_NAME = CategoryRepository::TABLE_NAME;

    /**
     * @param \ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue $columns
     *
     * @return string
     */
    protected function getOrderBy(ColumnPriorityQueue $columns)
    {
        return ' ORDER BY module ASC, c.title DESC, c.id DESC';
    }

    /**
     * @param \ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue $columns
     *
     * @return string
     */
    protected function getColumns(ColumnPriorityQueue $columns)
    {
        return 'c.*, m.name AS module';
    }

    /**
     * @return string
     */
    protected function getFrom()
    {
        return $this->getTableName() . ' AS c LEFT JOIN ' . $this->getTableName(ModuleRepository::TABLE_NAME) . ' AS m ON(m.id = c.module_id)';
    }
}
