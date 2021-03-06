<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Model\Repository;

use ACP3\Core\DataGrid\ColumnPriorityQueue;
use ACP3\Core\DataGrid\Model\Repository\AbstractDataGridRepository;
use Doctrine\DBAL\Query\QueryBuilder;

class GalleryDataGridRepository extends AbstractDataGridRepository
{
    public const TABLE_NAME = GalleryRepository::TABLE_NAME;

    /**
     * {@inheritdoc}
     */
    protected function getColumns(ColumnPriorityQueue $gridColumns)
    {
        return [
            'main.id',
            'main.active',
            'main.start',
            'main.end',
            'main.title',
            'COUNT(p.gallery_id) AS pictures',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function addJoin(QueryBuilder $queryBuilder)
    {
        $queryBuilder->leftJoin(
            'main',
            $this->getTableName(PictureRepository::TABLE_NAME),
            'p',
            'main.id = p.gallery_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function addGroupBy(QueryBuilder $queryBuilder)
    {
        $queryBuilder->addGroupBy([
            'main.id',
            'main.active',
            'main.start',
            'main.end',
            'main.title',
        ]);
    }
}
