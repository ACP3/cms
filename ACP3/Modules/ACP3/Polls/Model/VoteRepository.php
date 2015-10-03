<?php
namespace ACP3\Modules\ACP3\Polls\Model;

use ACP3\Core;

/**
 * Class VoteRepository
 * @package ACP3\Modules\ACP3\Polls\Model
 */
class VoteRepository extends Core\Model
{
    const TABLE_NAME = 'poll_votes';

    /**
     * @param $pollId
     * @param $userId
     * @param $ipAddress
     *
     * @return mixed
     */
    public function getVotesByUserId($pollId, $userId, $ipAddress)
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE poll_id = ? AND (user_id = ? OR ip = ?)', [$pollId, $userId, $ipAddress]);
    }

    /**
     * @param $pollId
     * @param $ipAddress
     *
     * @return mixed
     */
    public function getVotesByIpAddress($pollId, $ipAddress)
    {
        return $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE poll_id = ? AND ip = ?', [$pollId, $ipAddress]);
    }
}