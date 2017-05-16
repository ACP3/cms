<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Comments\Model\Repository;


use ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue;
use ACP3\Core\Helpers\DataGrid\Model\Repository\AbstractDataGridRepository;
use ACP3\Modules\ACP3\Users\Model\Repository\UsersRepository;
use Doctrine\DBAL\Query\QueryBuilder;

class CommentsDataGridRepository extends AbstractDataGridRepository
{
    const TABLE_NAME = CommentsRepository::TABLE_NAME;

    /**
     * @param int $moduleId
     *
     * @return array
     */
    public function getAllByModuleInAcp($moduleId)
    {
        return $this->db->fetchAll(
            'SELECT IF(c.user_id IS NULL, c.name, u.nickname) AS `name`, c.id, c.ip, c.user_id, c.date, c.message FROM ' . $this->getTableName() . ' AS c LEFT JOIN ' . $this->getTableName(\ACP3\Modules\ACP3\Users\Model\Repository\UsersRepository::TABLE_NAME) . ' AS u ON (u.id = c.user_id) WHERE c.module_id = ? ORDER BY c.entry_id ASC, c.date ASC',
            [$moduleId]
        );
    }

    /**
     * @inheritdoc
     */
    protected function getColumns(ColumnPriorityQueue $gridColumns)
    {
        return [
            'IF(main.user_id IS NULL, main.name, u.nickname) AS `name`',
            'main.id',
            'main.ip',
            'main.user_id',
            'main.date',
            'main.message'
        ];
    }

    /**
     * @inheritdoc
     */
    protected function addJoin(QueryBuilder $queryBuilder)
    {
        $queryBuilder->leftJoin(
            'main',
            $this->getTableName(UsersRepository::TABLE_NAME),
            'u',
            'main.user_id = u.id');
    }

    /**
     * @inheritdoc
     */
    protected function setOrderBy(ColumnPriorityQueue $gridColumns, QueryBuilder $queryBuilder)
    {
        $queryBuilder->addOrderBy('main.entry_id', 'ASC');

        parent::setOrderBy($gridColumns, $queryBuilder);
    }
}
