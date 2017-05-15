<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Comments\Model\Repository;


use ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue;
use ACP3\Core\Model\Repository\AbstractDataGridRepository;
use ACP3\Modules\ACP3\System\Model\Repository\ModulesRepository;
use Doctrine\DBAL\Query\QueryBuilder;

class CommentsByModuleDataGridRepository extends AbstractDataGridRepository
{
    const TABLE_NAME = CommentRepository::TABLE_NAME;

//    /**
//     * @return array
//     */
//    public function getCommentsGroupedByModule()
//    {
//        return $this->db->fetchAll('SELECT c.module_id, m.name AS module, COUNT(c.module_id) AS `comments_count` FROM ' . $this->getTableName() . ' AS c JOIN ' . $this->getTableName(ModulesRepository::TABLE_NAME) . ' AS m ON(m.id = c.module_id) GROUP BY c.module_id ORDER BY m.name');
//    }

    /**
     * @inheritdoc
     */
    protected function getColumns(ColumnPriorityQueue $gridColumns)
    {
        return [
            'main.module_id',
            'COUNT(main.module_id) AS `comments_count`',
            'm.name AS module'
        ];
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    protected function addGroupBy(QueryBuilder $queryBuilder)
    {
        $queryBuilder->addGroupBy('main.module_id');
    }
}
