<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Model;


use ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue;
use ACP3\Core\Model\DataGridRepository;

/**
 * Class GalleryDataGridRepository
 * @package ACP3\Modules\ACP3\Gallery\Model
 */
class GalleryDataGridRepository extends DataGridRepository
{
    const TABLE_NAME = GalleryRepository::TABLE_NAME;

    /**
     * @param \ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue $columns
     *
     * @return string
     */
    protected function getColumns(ColumnPriorityQueue $columns)
    {
        return 'g.id, g.start, g.end, g.title, COUNT(p.gallery_id) AS picture';
    }

    /**
     * @return string
     */
    protected function getFrom()
    {
        return $this->getTableName() . ' AS g LEFT JOIN ' . $this->getTableName(PictureRepository::TABLE_NAME) . ' AS p ON(g.id = p.gallery_id) GROUP BY g.id';
    }

    /**
     * @param \ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue $columns
     *
     * @return string
     */
    protected function getOrderBy(ColumnPriorityQueue $columns)
    {
        return ' ORDER BY g.start DESC, g.end DESC, g.id DESC';
    }
}
