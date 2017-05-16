<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Polls\Model\Repository;

use ACP3\Core;

class PollsRepository extends Core\Model\Repository\AbstractRepository
{
    use Core\Model\Repository\PublicationPeriodAwareTrait;

    const TABLE_NAME = 'polls';

    /**
     * @param int $pollId
     * @param string $time
     * @param bool $multiple
     *
     * @return bool
     */
    public function pollExists($pollId, $time = '', $multiple = false)
    {
        $where = !empty($time) ? ' AND ' . $this->getPublicationPeriod() : '';
        $multiple = ($multiple === true) ? ' AND multiple = :multiple' : '';
        $query = 'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id = :id' . $where . $multiple;
        return $this->db->fetchColumn($query, ['id' => $pollId, 'time' => $time, 'multiple' => 1]) > 0;
    }

    /**
     * @param int $pollId
     *
     * @return array
     */
    public function getOneByIdWithTotalVotes($pollId)
    {
        return $this->db->fetchAssoc(
            'SELECT p.*, COUNT(pv.poll_id) AS total_votes FROM ' . $this->getTableName() . ' AS p LEFT JOIN ' . $this->getTableName(PollVotesRepository::TABLE_NAME) . ' AS pv ON(p.id = pv.poll_id) WHERE p.id = ?',
            [$pollId]
        );
    }

    public function countAll($time = ''): int
    {
        $where = empty($time) === false ? ' WHERE start <= :time' : '';
        return (int)$this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->getTableName()}{$where}",
            ['time' => $time]
        );
    }

    /**
     * @param int $userId
     * @param string $ipAddress
     * @param string $time
     * @param string $limitStart
     * @param string $resultsPerPage
     * @return array
     */
    public function getAll($userId = 0, $ipAddress = '', $time = '', $limitStart = '', $resultsPerPage = '')
    {
        $where = empty($time) === false ? ' WHERE p.start <= :time' : '';
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll(
            'SELECT p.id, p.start, p.end, p.title, COUNT(pv.poll_id) AS votes, IF(pv.`ip` = :ip OR pv.`user_id` = :user_id, 1, 0) AS `has_voted` FROM ' . $this->getTableName() . ' AS p LEFT JOIN ' . $this->getTableName(PollVotesRepository::TABLE_NAME) . ' AS pv ON(p.id = pv.poll_id)' . $where . ' GROUP BY p.id ORDER BY p.start DESC, p.end DESC, p.id DESC' . $limitStmt,
            ['time' => $time, 'ip' => $ipAddress, 'user_id' => $userId]
        );
    }

    /**
     * @param string $time
     * @param int $userId
     * @param string $ipAddress
     * @return array
     */
    public function getLatestPoll($time, int $userId = 0, string $ipAddress = '')
    {
        $period = $this->getPublicationPeriod('p.');
        return $this->db->fetchAssoc(
            'SELECT p.id, p.title, p.multiple, COUNT(pv.poll_id) AS total_votes, IF(pv.`user_id` = :user_id OR pv.`ip` = :ip, 1, 0) AS `has_voted` FROM ' . $this->getTableName() . ' AS p LEFT JOIN ' . $this->getTableName(PollVotesRepository::TABLE_NAME) . ' AS pv ON(p.id = pv.poll_id) WHERE ' . $period . ' GROUP BY p.id ORDER BY p.start DESC LIMIT 1',
            ['time' => $time, 'user_id' => $userId, 'ip' => $ipAddress]
        );
    }
}
