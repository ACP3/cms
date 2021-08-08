<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Repository;

use ACP3\Core;

class GuestbookRepository extends Core\Repository\AbstractRepository implements Core\Repository\FloodBarrierAwareRepositoryInterface
{
    public const TABLE_NAME = 'guestbook';

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function resultExists(int $guestbookId): bool
    {
        return (int) $this->db->fetchColumn(
                'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id = :id',
                ['id' => $guestbookId]
            ) > 0;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function countAll(?int $notify = null): int
    {
        $where = ($notify === 2) ? ' WHERE active = 1' : '';

        return (int) $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->getTableName()}{$where}");
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAll(?int $notify = null, ?int $limitStart = null, ?int $resultsPerPage = null): array
    {
        $where = ($notify === 2) ? 'WHERE active = 1' : '';
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);

        return $this->db->fetchAll('SELECT IF(g.user_id IS NULL, g.name, u.nickname) AS `name`, IF(g.user_id IS NULL, g.website, u.website) AS `website`, IF(g.user_id IS NULL, g.mail, u.mail) AS `mail`, g.id, g.date, g.user_id, g.message FROM ' . $this->getTableName() . ' AS g LEFT JOIN ' . $this->getTableName(\ACP3\Modules\ACP3\Users\Repository\UserRepository::TABLE_NAME) . ' AS u ON(u.id = g.user_id) ' . $where . ' ORDER BY DATE DESC' . $limitStmt);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getLastDateFromIp(string $ipAddress): ?string
    {
        return $this->db->fetchColumn('SELECT MAX(`date`) FROM ' . $this->getTableName() . ' WHERE ip = ?', [$ipAddress]);
    }
}
