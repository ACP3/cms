<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Model\Repository;

use ACP3\Core\DataGrid\ColumnPriorityQueue;
use Doctrine\DBAL\Query\QueryBuilder;

class DataGridRepository extends \ACP3\Core\Model\Repository\DataGridRepository
{
    const TABLE_NAME = ShareRepository::TABLE_NAME;

    protected function getColumns(ColumnPriorityQueue $gridColumns)
    {
        return [
            'main.*',
            'AVG(sr.stars) AS average_rating',
            'COUNT(sr.id) AS ratings_count',
        ];
    }

    protected function addJoin(QueryBuilder $queryBuilder)
    {
        $queryBuilder->leftJoin(
            'main',
            $this->getTableName(ShareRatingsRepository::TABLE_NAME),
            'sr',
            'sr.share_id = main.id'
        );
    }

    protected function addGroupBy(QueryBuilder $queryBuilder)
    {
        $queryBuilder->addGroupBy('sr.share_id');
    }
}
