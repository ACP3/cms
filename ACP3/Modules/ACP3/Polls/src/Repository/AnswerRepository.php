<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\Repository;

use ACP3\Core;

class AnswerRepository extends Core\Repository\AbstractRepository
{
    public const TABLE_NAME = 'poll_answers';

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAnswersByPollId(int $pollId): array
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->getTableName() . ' WHERE poll_id = ? ORDER BY id ASC', [$pollId]);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAnswersWithVotesByPollId(int $pollId): array
    {
        return $this->db->fetchAll('SELECT pa.id, pa.text, COUNT(pv.answer_id) AS votes FROM ' . $this->getTableName() . ' AS pa LEFT JOIN ' . $this->getTableName(VoteRepository::TABLE_NAME) . ' AS pv ON(pa.id = pv.answer_id) WHERE pa.poll_id = ? GROUP BY pa.id, pa.text ORDER BY pa.id ASC', [$pollId]);
    }
}
