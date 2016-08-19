<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Polls\Model\Repository;

use ACP3\Core;

/**
 * Class VoteRepository
 * @package ACP3\Modules\ACP3\Polls\Model\Repository
 */
class VoteRepository extends Core\Model\AbstractRepository
{
    const TABLE_NAME = 'poll_votes';

    /**
     * @param int $pollId
     * @param int $userId
     * @param string $ipAddress
     *
     * @return int
     */
    public function getVotesByUserId($pollId, $userId, $ipAddress)
    {
        return (int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE poll_id = ? AND (user_id = ? OR ip = ?)', [$pollId, $userId, $ipAddress]);
    }

    /**
     * @param int $pollId
     * @param string $ipAddress
     *
     * @return int
     */
    public function getVotesByIpAddress($pollId, $ipAddress)
    {
        return (int)$this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE poll_id = ? AND ip = ?', [$pollId, $ipAddress]);
    }
}
