<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\Repository;

use ACP3\Core;

class VoteRepository extends Core\Repository\AbstractRepository
{
    public const TABLE_NAME = 'poll_votes';

    public function getVotesByUserId(int $pollId, int $userId, string $ipAddress): int
    {
        return (int) $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE poll_id = ? AND (user_id = ? OR ip = ?)', [$pollId, $userId, $ipAddress]);
    }

    public function getVotesByIpAddress(int $pollId, string $ipAddress): int
    {
        return (int) $this->db->fetchColumn('SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE poll_id = ? AND ip = ?', [$pollId, $ipAddress]);
    }
}
