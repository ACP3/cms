<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Model\Repository;

use ACP3\Core;

class GuestbookRepository extends Core\Model\Repository\AbstractRepository implements Core\Model\Repository\FloodBarrierAwareRepositoryInterface
{
    const TABLE_NAME = 'guestbook';

    /**
     * @param int $guestbookId
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function resultExists(int $guestbookId)
    {
        return (int) $this->db->fetchColumn(
                'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id = :id',
                ['id' => $guestbookId]
            ) > 0;
    }

    /**
     * @param int|null $notify
     *
     * @return bool|string
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function countAll(?int $notify = null)
    {
        $where = ($notify == 2) ? ' WHERE active = 1' : '';

        return $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->getTableName()}{$where}");
    }

    /**
     * @param int|null $notify
     * @param int|null $limitStart
     * @param int|null $resultsPerPage
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAll(?int $notify = null, ?int $limitStart = null, ?int $resultsPerPage = null)
    {
        $where = ($notify == 2) ? 'WHERE active = 1' : '';
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);

        return $this->db->fetchAll('SELECT IF(g.user_id IS NULL, g.name, u.nickname) AS `name`, IF(g.user_id IS NULL, g.website, u.website) AS `website`, IF(g.user_id IS NULL, g.mail, u.mail) AS `mail`, g.id, g.date, g.user_id, g.message FROM ' . $this->getTableName() . ' AS g LEFT JOIN ' . $this->getTableName(\ACP3\Modules\ACP3\Users\Model\Repository\UserRepository::TABLE_NAME) . ' AS u ON(u.id = g.user_id) ' . $where . ' ORDER BY DATE DESC' . $limitStmt);
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
        return $this->db->fetchColumn('SELECT MAX(`date`) FROM ' . $this->getTableName() . ' WHERE ip = ?', [$ipAddress]);
    }
}
