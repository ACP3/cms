<?php

namespace ACP3\Modules\ACP3\Polls;

use ACP3\Core;

/**
 * Class Model
 * @package ACP3\Modules\ACP3\Polls
 */
class Model extends Core\Model
{
    const TABLE_NAME = 'polls';
    const TABLE_NAME_ANSWERS = 'poll_answers';
    const TABLE_NAME_VOTES = 'poll_votes';

    /**
     * @param        $pollId
     * @param string $time
     * @param bool   $multiple
     *
     * @return bool
     */
    public function pollExists($pollId, $time = '', $multiple = false)
    {
        $where = !empty($time) ? ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)' : '';
        $multiple = ($multiple === true) ? ' AND multiple = :multiple' : '';
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE id = :id' . $where . $multiple, ['id' => $pollId, 'time' => $time, 'multiple' => 1]) > 0 ? true : false;
    }

    /**
     * @param $pollId
     *
     * @return array
     */
    public function getOneById($pollId)
    {
        return $this->db->fetchAssoc('SELECT * FROM ' . $this->getTableName() . ' WHERE id = :id', ['id' => $pollId]);
    }

    /**
     * @param $pollId
     *
     * @return array
     */
    public function getOneByIdWithTotalVotes($pollId)
    {
        return $this->db->fetchAssoc('SELECT p.*, COUNT(pv.poll_id) AS total_votes FROM ' . $this->getTableName() . ' AS p LEFT JOIN ' . $this->getTableName(static::TABLE_NAME_VOTES) . ' AS pv ON(p.id = pv.poll_id) WHERE p.id = ?', [$pollId]);
    }

    /**
     * @param $id
     *
     * @return array
     */
    public function getAnswersById($id)
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->getTableName(static::TABLE_NAME_ANSWERS) . ' WHERE poll_id = ? ORDER BY id ASC', [$id]);
    }

    /**
     * @param string $status
     *
     * @return array
     */
    public function countAll($status = '')
    {
        return $this->getAll($status);
    }

    /**
     * @param string $time
     * @param string $limitStart
     * @param string $resultsPerPage
     *
     * @return array
     */
    public function getAll($time = '', $limitStart = '', $resultsPerPage = '')
    {
        $where = empty($time) === false ? ' WHERE p.start <= :time' : '';
        $limitStmt = $this->buildLimitStmt($limitStart, $resultsPerPage);
        return $this->db->fetchAll('SELECT p.id, p.start, p.end, p.title, COUNT(pv.poll_id) AS votes FROM ' . $this->getTableName() . ' AS p LEFT JOIN ' . $this->getTableName(static::TABLE_NAME_VOTES) . ' AS pv ON(p.id = pv.poll_id)' . $where . ' GROUP BY p.id ORDER BY p.start DESC, p.end DESC, p.id DESC' . $limitStmt, ['time' => $time]);
    }

    /**
     * @param $pollId
     * @param $userId
     * @param $ipAddress
     *
     * @return mixed
     */
    public function getVotesByUserId($pollId, $userId, $ipAddress)
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName(static::TABLE_NAME_VOTES) . ' WHERE poll_id = ? AND (user_id = ? OR ip = ?)', [$pollId, $userId, $ipAddress]);
    }

    /**
     * @param $pollId
     * @param $ipAddress
     *
     * @return mixed
     */
    public function getVotesByIpAddress($pollId, $ipAddress)
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName(static::TABLE_NAME_VOTES) . ' WHERE poll_id = ? AND ip = ?', [$pollId, $ipAddress]);
    }

    /**
     * @param $pollId
     *
     * @return array
     */
    public function getAnswersByPollId($pollId)
    {
        return $this->db->fetchAll('SELECT pa.id, pa.text, COUNT(pv.answer_id) AS votes FROM ' . $this->getTableName(static::TABLE_NAME_ANSWERS) . ' AS pa LEFT JOIN ' . $this->getTableName(static::TABLE_NAME_VOTES) . ' AS pv ON(pa.id = pv.answer_id) WHERE pa.poll_id = ? GROUP BY pa.id ORDER BY pa.id ASC', [$pollId]);
    }

    /**
     * @param $time
     *
     * @return array
     */
    public function getLatestPoll($time)
    {
        $period = 'p.start = p.end AND p.start <= :time OR p.start != p.end AND :time BETWEEN p.start AND p.end';
        return $this->db->fetchAssoc('SELECT p.id, p.title, p.multiple, COUNT(pv.poll_id) AS total_votes FROM ' . $this->getTableName() . ' AS p LEFT JOIN ' . $this->getTableName(static::TABLE_NAME_VOTES) . ' AS pv ON(p.id = pv.poll_id) WHERE ' . $period . ' GROUP BY p.id ORDER BY p.start DESC LIMIT 1', ['time' => $time]);
    }

    /**
     * @return array
     */
    public function getAllInAcp()
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->getTableName() . ' ORDER BY START DESC, END DESC, id DESC');
    }
}
