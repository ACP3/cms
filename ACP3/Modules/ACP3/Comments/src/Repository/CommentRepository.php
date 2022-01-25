<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Repository;

use ACP3\Core\Repository\FloodBarrierAwareRepositoryInterface;
use ACP3\Modules\ACP3\System\Repository\ModulesRepository;
use ACP3\Modules\ACP3\Users\Repository\UserRepository;

class CommentRepository extends \ACP3\Core\Repository\AbstractRepository implements FloodBarrierAwareRepositoryInterface
{
    public const TABLE_NAME = 'comments';

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function resultExists(int $commentId): bool
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id = ?', [$commentId]) > 0;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function resultsExistByModuleId(int $moduleId): bool
    {
        return $this->db->fetchColumn(
                'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE module_id = ?',
                [$moduleId]
        ) > 0;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function countAll(int $moduleId = 0): int
    {
        if ($moduleId === 0) {
            return (int) $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName());
        }

        return (int) $this->db->fetchColumn(
            'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE module_id = ?',
            [$moduleId]
        );
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getOneById(int|string $entryId): array
    {
        return $this->db->fetchAssoc(
            'SELECT c.*, m.name AS module FROM ' . $this->getTableName() . ' AS c JOIN ' . $this->getTableName(ModulesRepository::TABLE_NAME) . ' AS m ON(m.id = c.module_id) WHERE c.id = ?',
            [$entryId]
        );
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getLastDateFromIp(string $ipAddress): ?string
    {
        return $this->db->fetchColumn(
            'SELECT MAX(`date`) FROM ' . $this->getTableName() . ' WHERE ip = ?',
            [$ipAddress]
        );
    }

    /**
     * @return array<array<string, mixed>>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAllByModule(int $moduleId, int $resultId, ?int $limitStart = null, ?int $resultsPerPage = null): array
    {
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);

        return $this->db->fetchAll(
            'SELECT IF(c.user_id IS NULL, c.name, u.nickname) AS `name`, c.user_id, c.date, c.message FROM ' . $this->getTableName() . ' AS c LEFT JOIN ' . $this->getTableName(UserRepository::TABLE_NAME) . ' AS u ON (u.id = c.user_id) WHERE c.module_id = ? AND c.entry_id = ? ORDER BY c.date ASC' . $limitStmt,
            [$moduleId, $resultId]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function countAllByModule(int $moduleId, int $resultId): int
    {
        return (int) $this->db->fetchColumn(
            'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE module_id = ? AND entry_id = ?',
            [$moduleId, $resultId]
        );
    }
}
