<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\Model\Repository;

use ACP3\Core;

class PollRepository extends Core\Model\Repository\AbstractRepository
{
    use Core\Model\Repository\PublicationPeriodAwareTrait;

    const TABLE_NAME = 'polls';

    /**
     * @param int    $pollId
     * @param string $time
     * @param bool   $multiple
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function pollExists(int $pollId, string $time = '', bool $multiple = false)
    {
        $where = !empty($time) ? ' AND ' . $this->getPublicationPeriod() : '';
        $multiple = ($multiple === true) ? ' AND multiple = :multiple' : '';
        $query = 'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id = :id' . $where . $multiple;

        return $this->db->fetchColumn($query, ['id' => $pollId, 'time' => $time, 'multiple' => 1]) > 0;
    }

    /**
     * @param int $pollId
     *
     * @return mixed
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getOneByIdWithTotalVotes(int $pollId)
    {
        return $this->db->fetchAssoc(
            'SELECT p.*, COUNT(pv.poll_id) AS total_votes FROM ' . $this->getTableName() . ' AS p LEFT JOIN ' . $this->getTableName(VoteRepository::TABLE_NAME) . ' AS pv ON(p.id = pv.poll_id) WHERE p.id = ?',
            [$pollId]
        );
    }

    /**
     * @param string   $time
     * @param int|null $limitStart
     * @param int|null $resultsPerPage
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAll(string $time = '', ?int $limitStart = null, ?int $resultsPerPage = null)
    {
        $where = empty($time) === false ? ' WHERE p.start <= :time' : '';
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);

        return $this->db->fetchAll(
            'SELECT p.id, p.start, p.end, p.title, COUNT(pv.poll_id) AS votes FROM ' . $this->getTableName() . ' AS p LEFT JOIN ' . $this->getTableName(VoteRepository::TABLE_NAME) . ' AS pv ON(p.id = pv.poll_id)' . $where . ' GROUP BY p.id ORDER BY p.start DESC, p.end DESC, p.id DESC' . $limitStmt,
            ['time' => $time]
        );
    }

    /**
     * @param string $time
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getLatestPoll(string $time)
    {
        $period = $this->getPublicationPeriod('p.');

        return $this->db->fetchAssoc(
            'SELECT p.id, p.title, p.multiple, COUNT(pv.poll_id) AS total_votes FROM ' . $this->getTableName() . ' AS p LEFT JOIN ' . $this->getTableName(VoteRepository::TABLE_NAME) . ' AS pv ON(p.id = pv.poll_id) WHERE ' . $period . ' GROUP BY p.id ORDER BY p.start DESC LIMIT 1',
            ['time' => $time]
        );
    }
}
