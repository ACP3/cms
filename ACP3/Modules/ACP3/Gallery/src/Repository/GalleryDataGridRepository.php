<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Repository;

use ACP3\Core\DataGrid\ColumnPriorityQueue;
use ACP3\Core\DataGrid\Repository\AbstractDataGridRepository;
use Doctrine\DBAL\Query\QueryBuilder;

class GalleryDataGridRepository extends AbstractDataGridRepository
{
    public const TABLE_NAME = GalleryRepository::TABLE_NAME;

    protected function getColumns(ColumnPriorityQueue $gridColumns): array
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

    protected function addJoin(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->leftJoin(
            'main',
            $this->getTableName(PictureRepository::TABLE_NAME),
            'p',
            'main.id = p.gallery_id'
        );
    }

    protected function addGroupBy(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->addGroupBy(
            'main.id',
            'main.active',
            'main.start',
            'main.end',
            'main.title',
        );
    }
}
