<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Polls\Model;


use ACP3\Core\Date;
use ACP3\Core\Helpers\Secure;
use ACP3\Core\Model\AbstractModel;
use ACP3\Modules\ACP3\Polls\Installer\Schema;
use ACP3\Modules\ACP3\Polls\Model\Repository\AnswerRepository;
use ACP3\Modules\ACP3\Polls\Model\Repository\PollRepository;
use ACP3\Modules\ACP3\Polls\Model\Repository\VoteRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class PollsModel
 * @package ACP3\Modules\ACP3\Polls\Model
 */
class PollsModel extends AbstractModel
{
    const EVENT_PREFIX = Schema::MODULE_NAME;

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
     * @param EventDispatcherInterface $eventDispatcher
     * @param Date $date
     * @param Secure $secure
     * @param PollRepository $pollRepository
     * @param AnswerRepository $answerRepository
     * @param VoteRepository $voteRepository
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        Date $date,
        Secure $secure,
        PollRepository $pollRepository,
        AnswerRepository $answerRepository,
        VoteRepository $voteRepository)
    {
        parent::__construct($eventDispatcher);

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

        return $this->save($this->pollRepository, $values, $pollId);
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
            if (empty($row['id'])) {
                if (!empty($row['text']) && !isset($row['delete'])) {
                    $data = [
                        'text' => $this->secure->strEncode($row['text']),
                        'poll_id' => $pollId
                    ];
                    $bool = $this->save($this->answerRepository, $data);
                }
            } elseif (isset($row['delete'])) {
                $this->answerRepository->delete((int)$row['id']);
            } elseif (!empty($row['text'])) {
                $data = [
                    'text' => $this->secure->strEncode($row['text']),
                ];
                $bool = $this->save($this->answerRepository, $data, (int)$row['id']);
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
