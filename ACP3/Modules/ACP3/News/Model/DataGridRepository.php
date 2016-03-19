<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\Model;


use ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue;
use ACP3\Modules\ACP3\Categories\Model\CategoryRepository;

/**
 * Class DataGridRepository
 * @package ACP3\Modules\ACP3\News\Model
 */
class DataGridRepository extends \ACP3\Core\Model\DataGridRepository
{
    const TABLE_NAME = NewsRepository::TABLE_NAME;

    /**
     * @param \ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue $columns
     *
     * @return string
     */
    protected function getOrderBy(ColumnPriorityQueue $columns)
    {
        return ' ORDER BY n.start DESC, n.end DESC, n.id DESC';
    }

    /**
     * @param \ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue $columns
     *
     * @return string
     */
    protected function getColumns(ColumnPriorityQueue $columns)
    {
        return 'n.*, c.title AS cat';
    }

    /**
     * @return string
     */
    protected function getFrom()
    {
        return $this->getTableName() . ' AS n LEFT JOIN ' . $this->getTableName(CategoryRepository::TABLE_NAME) . ' AS c ON(n.category_id = c.id)';
    }

}
