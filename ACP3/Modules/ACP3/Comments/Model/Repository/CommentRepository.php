<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Model\Repository;

use ACP3\Core\Model\Repository\AbstractRepository;
use ACP3\Core\Model\Repository\FloodBarrierAwareRepositoryInterface;
use ACP3\Modules\ACP3\System\Model\Repository\ModulesRepository;

class CommentRepository extends AbstractRepository implements FloodBarrierAwareRepositoryInterface
{
    const TABLE_NAME = 'comments';

    /**
     * @param int $commentId
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function resultExists(int $commentId)
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id = ?', [$commentId]) > 0;
    }

    /**
     * @param int $moduleId
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function resultsExistByModuleId(int $moduleId)
    {
        return $this->db->fetchColumn(
            'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE module_id = ?',
            [$moduleId]
        ) > 0;
    }

    /**
     * @param int $moduleId
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function countAll(int $moduleId = 0)
    {
        if ($moduleId === 0) {
            return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName());
        }

        return $this->db->fetchColumn(
            'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE module_id = ?',
            [$moduleId]
        );
    }

    /**
     * @param int $commentId
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getOneById($commentId)
    {
        return $this->db->fetchAssoc(
            'SELECT c.*, m.name AS module FROM ' . $this->getTableName() . ' AS c JOIN ' . $this->getTableName(ModulesRepository::TABLE_NAME) . ' AS m ON(m.id = c.module_id) WHERE c.id = ?',
            [$commentId]
        );
    }

    /**
     * @param string $ipAddress
     *
     * @return string
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getLastDateFromIp($ipAddress)
    {
        return $this->db->fetchColumn(
            'SELECT MAX(`date`) FROM ' . $this->getTableName() . ' WHERE ip = ?',
            [$ipAddress]
        );
    }

    /**
     * @param int      $moduleId
     * @param int      $resultId
     * @param int|null $limitStart
     * @param int|null $resultsPerPage
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAllByModule(int $moduleId, int $resultId, ?int $limitStart = null, ?int $resultsPerPage = null)
    {
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);

        return $this->db->fetchAll(
            'SELECT IF(c.user_id IS NULL, c.name, u.nickname) AS `name`, c.user_id, c.date, c.message FROM ' . $this->getTableName() . ' AS c LEFT JOIN ' . $this->getTableName(\ACP3\Modules\ACP3\Users\Model\Repository\UserRepository::TABLE_NAME) . ' AS u ON (u.id = c.user_id) WHERE c.module_id = ? AND c.entry_id = ? ORDER BY c.date ASC' . $limitStmt,
            [$moduleId, $resultId]
        );
    }

    /**
     * @param int $moduleId
     * @param int $resultId
     *
     * @return int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function countAllByModule(int $moduleId, int $resultId)
    {
        return (int) $this->db->fetchColumn(
            'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE module_id = ? AND entry_id = ?',
            [$moduleId, $resultId]
        );
    }
}
