<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\ViewProviders;

use ACP3\Modules\ACP3\Polls\Repository\AnswerRepository;
use ACP3\Modules\ACP3\Polls\Repository\PollRepository;

class PollResultViewProvider
{
    public function __construct(private PollRepository $pollRepository, private AnswerRepository $answerRepository)
    {
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(int $pollId): array
    {
        $question = $this->pollRepository->getOneByIdWithTotalVotes($pollId);
        $answers = $this->answerRepository->getAnswersWithVotesByPollId($pollId);
        $totalVotes = $question['total_votes'];

        foreach ($answers as $i => $answer) {
            $answers[$i]['percent'] = $totalVotes > 0 ? round(100 * $answer['votes'] / $totalVotes, 2) : 0;
        }

        return [
            'question' => $question['title'],
            'answers' => $answers,
            'total_votes' => $totalVotes,
        ];
    }
}
