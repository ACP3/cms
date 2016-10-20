<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Polls\Model;


use ACP3\Core\Helpers\Secure;
use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor;
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
     * @var Secure
     */
    protected $secure;
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
     * @param DataProcessor $dataProcessor
     * @param Secure $secure
     * @param PollRepository $pollRepository
     * @param AnswerRepository $answerRepository
     * @param VoteRepository $voteRepository
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        DataProcessor $dataProcessor,
        Secure $secure,
        PollRepository $pollRepository,
        AnswerRepository $answerRepository,
        VoteRepository $voteRepository
    ) {
        parent::__construct($eventDispatcher, $dataProcessor, $pollRepository);

        $this->secure = $secure;
        $this->answerRepository = $answerRepository;
        $this->voteRepository = $voteRepository;
    }

    /**
     * @param array $data
     * @param int $userId
     * @param null|int $pollId
     * @return bool|int
     */
    public function savePoll(array $data, $userId, $pollId = null)
    {
        $data['user_id'] = $userId;

        return $this->save($data, $pollId);
    }

    /**
     * @param array $answers
     * @param int $pollId
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
                    $bool = $this->answerRepository->insert($data);
                }
            } elseif (isset($row['delete'])) {
                $this->answerRepository->delete((int)$row['id']);
            } elseif (!empty($row['text'])) {
                $data = [
                    'text' => $this->secure->strEncode($row['text']),
                ];
                $bool = $this->answerRepository->update($data, (int)$row['id']);
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

    /**
     * @return array
     */
    protected function getAllowedColumns()
    {
        return [
            'start' => DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
            'end' => DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
            'title' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT,
            'multiple' => DataProcessor\ColumnTypes::COLUMN_TYPE_BOOLEAN,
            'user_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT
        ];
    }
}
