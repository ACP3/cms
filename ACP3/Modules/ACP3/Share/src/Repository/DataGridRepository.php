<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Repository;

use ACP3\Core\DataGrid\ColumnPriorityQueue;
use ACP3\Core\DataGrid\Repository\AbstractDataGridRepository;
use Doctrine\DBAL\Query\QueryBuilder;

class DataGridRepository extends AbstractDataGridRepository
{
    public const TABLE_NAME = ShareRepository::TABLE_NAME;

    protected function getColumns(ColumnPriorityQueue $gridColumns): array
    {
        return [
            'main.*',
            'AVG(sr.stars) AS average_rating',
            'COUNT(sr.id) AS ratings_count',
        ];
    }

    protected function addJoin(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->leftJoin(
            'main',
            $this->getTableName(ShareRatingsRepository::TABLE_NAME),
            'sr',
            'sr.share_id = main.id'
        );
    }

    protected function addGroupBy(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->addGroupBy([
            'sr.share_id',
            'main.id',
            'main.uri',
            'main.active',
            'main.services',
            'main.ratings_active',
        ]);
    }
}
