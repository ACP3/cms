<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Polls\Model\Repository;

use ACP3\Core;

/**
 * Class AnswerRepository
 * @package ACP3\Modules\ACP3\Polls\Model\Repository
 */
class AnswerRepository extends Core\Model\AbstractRepository
{
    const TABLE_NAME = 'poll_answers';

    /**
     * @param int $pollId
     *
     * @return array
     */
    public function getAnswersByPollId($pollId)
    {
        return $this->db->fetchAll('SELECT * FROM ' . $this->getTableName() . ' WHERE poll_id = ? ORDER BY id ASC', [$pollId]);
    }

    /**
     * @param int $pollId
     *
     * @return array
     */
    public function getAnswersWithVotesByPollId($pollId)
    {
        return $this->db->fetchAll('SELECT pa.id, pa.text, COUNT(pv.answer_id) AS votes FROM ' . $this->getTableName() . ' AS pa LEFT JOIN ' . $this->getTableName(VoteRepository::TABLE_NAME) . ' AS pv ON(pa.id = pv.answer_id) WHERE pa.poll_id = ? GROUP BY pa.id ORDER BY pa.id ASC', [$pollId]);
    }
}
