<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Model\Repository;

use ACP3\Core\DataGrid\ColumnPriorityQueue;
use ACP3\Core\DataGrid\Model\Repository\AbstractDataGridRepository;
use ACP3\Modules\ACP3\System\Model\Repository\ModulesRepository;
use Doctrine\DBAL\Query\QueryBuilder;

class CommentsByModuleDataGridRepository extends AbstractDataGridRepository
{
    public const TABLE_NAME = CommentRepository::TABLE_NAME;

    /**
     * {@inheritdoc}
     */
    protected function getColumns(ColumnPriorityQueue $gridColumns)
    {
        return [
            'main.module_id',
            'COUNT(main.module_id) AS `comments_count`',
            'm.name AS module',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function addJoin(QueryBuilder $queryBuilder)
    {
        $queryBuilder->leftJoin(
            'main',
            $this->getTableName(ModulesRepository::TABLE_NAME),
            'm',
            'main.module_id = m.id'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function addGroupBy(QueryBuilder $queryBuilder)
    {
        $queryBuilder->addGroupBy(['main.module_id', 'm.name']);
    }
}
