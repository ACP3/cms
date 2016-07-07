<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Polls\Model;


use ACP3\Core\Date;
use ACP3\Core\Helpers\Secure;
use ACP3\Modules\ACP3\Polls\Model\Repository\AnswerRepository;
use ACP3\Modules\ACP3\Polls\Model\Repository\PollRepository;
use ACP3\Modules\ACP3\Polls\Model\Repository\VoteRepository;

/**
 * Class PollsModel
 * @package ACP3\Modules\ACP3\Polls\Model
 */
class PollsModel
{
    /**
     * @var Date
     */
    protected $date;
    /**
     * @var Secure
     */
    protected $secure;
    /**
     * @var PollRepository
     */
    protected $pollRepository;
    /**
     * @var AnswerRepository
     */
    protected $answerRepository;
    /**
     * @var VoteRepository
     */
    protected $voteRepository;

    /**
     * PollsModel constructor.
     * @param Date $date
     * @param Secure $secure
     * @param PollRepository $pollRepository
     * @param AnswerRepository $answerRepository
     * @param VoteRepository $voteRepository
     */
    public function __construct(
        Date $date,
        Secure $secure,
        PollRepository $pollRepository,
        AnswerRepository $answerRepository,
        VoteRepository $voteRepository)
    {
        $this->date = $date;
        $this->secure = $secure;
        $this->pollRepository = $pollRepository;
        $this->answerRepository = $answerRepository;
        $this->voteRepository = $voteRepository;
    }

    /**
     * @param array $formData
     * @param int $userId
     * @param null|int $pollId
     * @return bool|int
     */
    public function savePoll(array $formData, $userId, $pollId = null)
    {
        $values = [
            'start' => $this->date->toSQL($formData['start']),
            'end' => $this->date->toSQL($formData['end']),
            'title' => $this->secure->strEncode($formData['title']),
            'multiple' => isset($formData['multiple']) ? '1' : '0',
            'user_id' => $userId,
        ];

        if (intval($pollId)) {
            return $this->pollRepository->update($values, $pollId);
        }

        return $this->pollRepository->insert($values);
    }

    /**
     * @param array $answers
     * @param int   $pollId
     *
     * @return bool|int
     */
    public function saveAnswers(array $answers, $pollId)
    {
        $bool = false;
        foreach ($answers as $row) {
            // Neue Antwort hinzufügen
            if (empty($row['id'])) {
                // Neue Antwort nur hinzufügen, wenn die Löschen-Checkbox nicht gesetzt wurde
                if (!empty($row['text']) && !isset($row['delete'])) {
                    $bool = $this->answerRepository->insert(
                        ['text' => $this->secure->strEncode($row['text']), 'poll_id' => $pollId]
                    );
                }
            } elseif (isset($row['delete'])) { // Antwort mitsamt Stimmen löschen
                $this->answerRepository->delete((int)$row['id']);
                $this->voteRepository->delete((int)$row['id'], 'answer_id');
            } elseif (!empty($row['text'])) { // Antwort aktualisieren
                $bool = $this->answerRepository->update(
                    ['text' => $this->secure->strEncode($row['text'])],
                    (int)$row['id']
                );
            }
        }

        return $bool;
    }

    /**
     * @param int $pollId
     * @return bool|int
     */
    public function resetVotesByPollId($pollId)
    {
        return $this->voteRepository->delete($pollId, 'poll_id');
    }
}
