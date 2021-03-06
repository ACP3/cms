<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\ViewProviders;

use ACP3\Modules\ACP3\Polls\Model\Repository\AnswerRepository;
use ACP3\Modules\ACP3\Polls\Model\Repository\PollRepository;

class PollVoteViewProvider
{
    /**
     * @var \ACP3\Modules\ACP3\Polls\Model\Repository\PollRepository
     */
    private $pollRepository;
    /**
     * @var \ACP3\Modules\ACP3\Polls\Model\Repository\AnswerRepository
     */
    private $answerRepository;

    public function __construct(
        PollRepository $pollRepository,
        AnswerRepository $answerRepository
    ) {
        $this->pollRepository = $pollRepository;
        $this->answerRepository = $answerRepository;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(int $pollId): array
    {
        $poll = $this->pollRepository->getOneById($pollId);

        return [
            'question' => $poll['title'],
            'multiple' => $poll['multiple'],
            'answers' => $this->answerRepository->getAnswersByPollId($pollId),
        ];
    }
}
