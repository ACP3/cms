<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\Model;


use ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue;

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
     * @return array
     */
    public function getAllInAcp(ColumnPriorityQueue $columns)
    {
        return $this->db->fetchAll('SELECT n.*, c.title AS cat FROM ' . $this->getTableName() . ' AS n, ' . $this->getTableName(\ACP3\Modules\ACP3\Categories\Model\CategoryRepository::TABLE_NAME) . ' AS c WHERE n.category_id = c.id ORDER BY n.start DESC, n.end DESC, n.id DESC');
    }
}
