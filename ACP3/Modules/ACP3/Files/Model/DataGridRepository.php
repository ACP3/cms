<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\Model;


use ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue;

/**
 * Class DataGridRepository
 * @package ACP3\Modules\ACP3\Files\Model
 */
class DataGridRepository extends \ACP3\Core\Model\DataGridRepository
{
    const TABLE_NAME = FilesRepository::TABLE_NAME;

    /**
     * @param \ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue $columns
     *
     * @return array
     */
    public function getAllInAcp(ColumnPriorityQueue $columns)
    {
        return $this->db->fetchAll('SELECT f.*, c.title AS cat FROM ' . $this->getTableName() . ' AS f, ' . $this->getTableName(\ACP3\Modules\ACP3\Categories\Model\CategoryRepository::TABLE_NAME) . ' AS c WHERE f.category_id = c.id ORDER BY f.start DESC, f.end DESC, f.id DESC');
    }
}
