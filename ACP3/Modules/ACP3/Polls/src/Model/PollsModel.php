<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\Model;

use ACP3\Core\Helpers\Secure;
use ACP3\Core\Model\AbstractModel;
use ACP3\Core\Model\DataProcessor;
use ACP3\Core\Model\UpdatedAtAwareModelInterface;
use ACP3\Modules\ACP3\Polls\Installer\Schema;
use ACP3\Modules\ACP3\Polls\Repository\AnswerRepository;
use ACP3\Modules\ACP3\Polls\Repository\PollRepository;
use ACP3\Modules\ACP3\Polls\Repository\VoteRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PollsModel extends AbstractModel implements UpdatedAtAwareModelInterface
{
    public const EVENT_PREFIX = Schema::MODULE_NAME;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        DataProcessor $dataProcessor,
        protected Secure $secure,
        PollRepository $pollRepository,
        protected AnswerRepository $answerRepository,
        protected VoteRepository $voteRepository
    ) {
        parent::__construct($eventDispatcher, $dataProcessor, $pollRepository);
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $rawData, ?int $entryId = null): int
    {
        $rawData['updated_at'] = 'now';

        return parent::save($rawData, $entryId);
    }

    /**
     * @param array<string, mixed>[] $answers
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function saveAnswers(array $answers, int $pollId): int
    {
        $result = 0;
        foreach ($answers as $row) {
            if (empty($row['id'])) {
                if (!empty($row['text']) && !isset($row['delete'])) {
                    $data = [
                        'text' => $this->secure->strEncode($row['text']),
                        'poll_id' => $pollId,
                    ];
                    $result = $this->answerRepository->insert($data);
                }
            } elseif (isset($row['delete'])) {
                $this->answerRepository->delete((int) $row['id']);
            } elseif (!empty($row['text'])) {
                $data = [
                    'text' => $this->secure->strEncode($row['text']),
                ];
                $result = $this->answerRepository->update($data, (int) $row['id']);
            }
        }

        return $result;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function resetVotesByPollId(int $pollId): int
    {
        return $this->voteRepository->delete($pollId, 'poll_id');
    }

    /**
     * {@inheritDoc}
     */
    protected function getAllowedColumns(): array
    {
        return [
            'start' => DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
            'end' => DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
            'updated_at' => DataProcessor\ColumnTypes::COLUMN_TYPE_DATETIME,
            'title' => DataProcessor\ColumnTypes::COLUMN_TYPE_TEXT,
            'multiple' => DataProcessor\ColumnTypes::COLUMN_TYPE_BOOLEAN,
            'user_id' => DataProcessor\ColumnTypes::COLUMN_TYPE_INT,
        ];
    }
}
