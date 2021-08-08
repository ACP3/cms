<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Repository;

use ACP3\Core\DataGrid\ColumnPriorityQueue;
use ACP3\Core\DataGrid\Repository\AbstractDataGridRepository;

class GalleryPicturesDataGridRepository extends AbstractDataGridRepository
{
    public const TABLE_NAME = PictureRepository::TABLE_NAME;

    /**
     * {@inheritdoc}
     */
    protected function getColumns(ColumnPriorityQueue $gridColumns)
    {
        return array_merge(
            parent::getColumns($gridColumns),
            [
                '(SELECT MIN(pmin.pic) FROM ' . $this->getTableName() . ' AS pmin WHERE pmin.gallery_id = main.gallery_id) AS `first`',
                '(SELECT MAX(pmax.pic) FROM ' . $this->getTableName() . ' AS pmax WHERE pmax.gallery_id = main.gallery_id) AS `last`',
            ]
        );
    }
}
