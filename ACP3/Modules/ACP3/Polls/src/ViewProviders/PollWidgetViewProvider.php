<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\ViewProviders;

use ACP3\Core\I18n\Translator;
use ACP3\Modules\ACP3\Polls\Repository\AnswerRepository;

class PollWidgetViewProvider
{
    /**
     * @var \ACP3\Modules\ACP3\Polls\Repository\AnswerRepository
     */
    private $answerRepository;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;

    public function __construct(
        AnswerRepository $answerRepository,
        Translator $translator
    ) {
        $this->answerRepository = $answerRepository;
        $this->translator = $translator;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(array $poll): array
    {
        $answers = [];

        if (!empty($poll)) {
            $answers = $this->answerRepository->getAnswersWithVotesByPollId($poll['id']);

            $totalVotes = $poll['total_votes'];

            foreach ($answers as $i => $answer) {
                $votes = (int) $answers[$i]['votes'];
                $answers[$i]['votes'] = ($votes > 1)
                    ? $this->translator->t('polls', 'number_of_votes', ['%votes%' => $votes])
                    : $this->translator->t('polls', ($votes === 1 ? 'one_vote' : 'no_votes'));
                $answers[$i]['percent'] = $totalVotes > 0 ? round(100 * $votes / $totalVotes, 2) : 0;
            }
        }

        return [
            'sidebar_polls' => $poll,
            'sidebar_poll_answers' => $answers,
        ];
    }
}
