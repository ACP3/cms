<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\ViewProviders;

use ACP3\Core\Helpers\Forms;
use ACP3\Modules\ACP3\Polls\Repository\AnswerRepository;
use ACP3\Modules\ACP3\Polls\Repository\PollRepository;

class PollVoteViewProvider
{
    public function __construct(private readonly PollRepository $pollRepository, private readonly AnswerRepository $answerRepository, private readonly Forms $formsHelper)
    {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(int $pollId): array
    {
        $poll = $this->pollRepository->getOneById($pollId);

        return [
            'question' => $poll['title'],
            'multiple' => $poll['multiple'],
            'answers' => $this->fetchAnswers($poll['multiple'], $pollId),
        ];
    }

    /**
     * @return array<string, mixed>[]
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function fetchAnswers(bool $isMultipleChoice, int $pollId): array
    {
        $answers = $this->answerRepository->getAnswersByPollId($pollId);

        $mappedAnswers = [];
        foreach ($answers as $answer) {
            $mappedAnswers[$answer['id']] = $answer['text'];
        }

        if ($isMultipleChoice) {
            return $this->formsHelper->checkboxGenerator('answer', $mappedAnswers);
        }

        return $this->formsHelper->choicesGenerator('answer', $mappedAnswers, '', 'checked');
    }
}
