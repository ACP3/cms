<?php

namespace ACP3\Modules\ACP3\Guestbook\Model;

use ACP3\Core;

/**
 * Class GuestbookRepository
 * @package ACP3\Modules\ACP3\Guestbook\Model
 */
class GuestbookRepository extends Core\Model\AbstractRepository implements Core\Model\FloodBarrierAwareRepositoryInterface
{
    const TABLE_NAME = 'guestbook';

    /**
     * @param int $guestbookId
     *
     * @return bool
     */
    public function resultExists($guestbookId)
    {
        return ((int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id = :id',
                ['id' => $guestbookId]) > 0);
    }

    /**
     * @param int $guestbookId
     *
     * @return array
     */
    public function getOneById($guestbookId)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->getTableName() . ' WHERE id = ?', [$guestbookId]);
    }

    /**
     * @param string $notify
     *
     * @return int
     */
    public function countAll($notify = '')
    {
        $where = ($notify == 2) ? 'WHERE active = 1' : '';
        return $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->getTableName()} {$where}");
    }

    /**
     * @param string $notify
     * @param string $limitStart
     * @param string $resultsPerPage
     *
     * @return array
     */
    public function getAll($notify = '', $limitStart = '', $resultsPerPage = '')
    {
        $where = ($notify == 2) ? 'WHERE active = 1' : '';
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll('SELECT IF(g.user_id IS NULL, g.name, u.nickname) AS `name`, IF(g.user_id IS NULL, g.website, u.website) AS `website`, IF(g.user_id IS NULL, g.mail, u.mail) AS `mail`, g.id, g.date, g.user_id, g.message FROM ' . $this->getTableName() . ' AS g LEFT JOIN ' . $this->getTableName(\ACP3\Modules\ACP3\Users\Model\UserRepository::TABLE_NAME) . ' AS u ON(u.id = g.user_id) ' . $where . ' ORDER BY DATE DESC' . $limitStmt);
    }

    /**
     * @param string $ipAddress
     *
     * @return mixed
     */
    public function getLastDateFromIp($ipAddress)
    {
        return $this->db->fetchColumn('SELECT MAX(date) FROM ' . $this->getTableName() . ' WHERE ip = ?', [$ipAddress]);
    }

    /**
     * @return array
     */
    public function getAllInAcp()
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->getTableName() . ' ORDER BY `date` DESC, id DESC');
    }
}
