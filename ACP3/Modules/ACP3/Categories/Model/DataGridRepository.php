<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Categories\Model;


use ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue;

class DataGridRepository extends \ACP3\Core\Model\DataGridRepository
{
    const TABLE_NAME = CategoryRepository::TABLE_NAME;

    /**
     * @param \ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue $columns
     *
     * @return array
     */
    public function getAllInAcp(ColumnPriorityQueue $columns)
    {
        return $this->db->fetchAll('SELECT c.*, m.name AS module FROM ' . $this->getTableName() . ' AS c JOIN ' . $this->getTableName(\ACP3\Modules\ACP3\System\Model\ModuleRepository::TABLE_NAME) . ' AS m ON(m.id = c.module_id) ORDER BY m.name ASC, c.title DESC, c.id DESC');
    }
}
