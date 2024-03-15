<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Repository;

use ACP3\Core\DataGrid\ColumnPriorityQueue;
use ACP3\Modules\ACP3\System\Repository\ModulesRepository;
use Doctrine\DBAL\Query\QueryBuilder;

class CommentsByModuleDataGridRepository extends \ACP3\Core\DataGrid\Repository\AbstractDataGridRepository
{
    public const TABLE_NAME = CommentRepository::TABLE_NAME;

    protected function getColumns(ColumnPriorityQueue $gridColumns): array
    {
        return [
            'main.module_id',
            'COUNT(main.module_id) AS `comments_count`',
            'm.name AS module',
        ];
    }

    protected function addJoin(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->leftJoin(
            'main',
            $this->getTableName(ModulesRepository::TABLE_NAME),
            'm',
            'main.module_id = m.id'
        );
    }

    protected function addGroupBy(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->addGroupBy('main.module_id', 'm.name');
    }
}
