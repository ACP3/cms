<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Polls\Controller\Widget\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Polls;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Polls\Controller\Widget\Index
 */
class Index extends Core\Controller\AbstractWidgetAction
{
    /**
     * @var Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\Polls\Model\Repository\PollRepository
     */
    protected $pollRepository;
    /**
     * @var \ACP3\Modules\ACP3\Polls\Model\Repository\AnswerRepository
     */
    protected $answerRepository;
    /**
     * @var \ACP3\Modules\ACP3\Polls\Model\Repository\VoteRepository
     */
    protected $voteRepository;

    /**
     * @param \ACP3\Core\Controller\Context\WidgetContext $context
     * @param Core\Date $date
     * @param \ACP3\Modules\ACP3\Polls\Model\Repository\PollRepository $pollRepository
     * @param \ACP3\Modules\ACP3\Polls\Model\Repository\AnswerRepository $answerRepository
     * @param \ACP3\Modules\ACP3\Polls\Model\Repository\VoteRepository $voteRepository
     */
    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Core\Date $date,
        Polls\Model\Repository\PollRepository $pollRepository,
        Polls\Model\Repository\AnswerRepository $answerRepository,
        Polls\Model\Repository\VoteRepository $voteRepository
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->pollRepository = $pollRepository;
        $this->answerRepository = $answerRepository;
        $this->voteRepository = $voteRepository;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $poll = $this->pollRepository->getLatestPoll($this->date->getCurrentDateTime());
        $answers = [];

        $this->setTemplate('Polls/Widget/index.vote.tpl');

        if (!empty($poll)) {
            $answers = $this->answerRepository->getAnswersWithVotesByPollId($poll['id']);

            if ($this->hasAlreadyVoted($poll['id'])) {
                $totalVotes = $poll['total_votes'];

                $cAnswers = count($answers);
                for ($i = 0; $i < $cAnswers; ++$i) {
                    $votes = $answers[$i]['votes'];
                    $answers[$i]['votes'] = ($votes > 1)
                        ? $this->translator->t('polls', 'number_of_votes', ['%votes%' => $votes])
                        : $this->translator->t('polls', ($votes == 1 ? 'one_vote' : 'no_votes'));
                    $answers[$i]['percent'] = $totalVotes > 0 ? round(100 * $votes / $totalVotes, 2) : '0';
                }

                $this->setTemplate('Polls/Widget/index.result.tpl');
            }
        }

        return [
            'sidebar_polls' => $poll,
            'sidebar_poll_answers' => $answers
        ];
    }

    /**
     * @param int $pollId
     * @return int
     */
    protected function hasAlreadyVoted($pollId)
    {
        // Check, whether the logged user has already voted
        if ($this->user->isAuthenticated() === true) {
            $votes = $this->voteRepository->getVotesByUserId(
                $pollId,
                $this->user->getUserId(),
                $this->request->getServer()->get('REMOTE_ADDR', '')
            );
        } else { // For guest users check against the ip address
            $votes = $this->voteRepository->getVotesByIpAddress(
                $pollId,
                $this->request->getServer()->get('REMOTE_ADDR', '')
            );
        }

        return $votes > 0;
    }
}
