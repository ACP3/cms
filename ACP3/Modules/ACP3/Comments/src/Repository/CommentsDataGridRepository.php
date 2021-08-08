<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Repository;

use ACP3\Core\DataGrid\ColumnPriorityQueue;
use ACP3\Core\DataGrid\Repository\AbstractDataGridRepository;
use ACP3\Modules\ACP3\Users\Repository\UserRepository;
use Doctrine\DBAL\Query\QueryBuilder;

class CommentsDataGridRepository extends AbstractDataGridRepository
{
    public const TABLE_NAME = CommentRepository::TABLE_NAME;

    /**
     * {@inheritdoc}
     */
    protected function getColumns(ColumnPriorityQueue $gridColumns)
    {
        return [
            'IF(main.user_id IS NULL, main.name, u.nickname) AS `name`',
            'main.id',
            'main.ip',
            'main.user_id',
            'main.date',
            'main.message',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function addJoin(QueryBuilder $queryBuilder)
    {
        $queryBuilder->leftJoin(
            'main',
            $this->getTableName(UserRepository::TABLE_NAME),
            'u',
            'main.user_id = u.id'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function setOrderBy(ColumnPriorityQueue $gridColumns, QueryBuilder $queryBuilder)
    {
        $queryBuilder->addOrderBy('main.entry_id', 'ASC');

        parent::setOrderBy($gridColumns, $queryBuilder);
    }
}
