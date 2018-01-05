<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Polls\View\Block\Frontend;

use ACP3\Core\View\Block\AbstractBlock;
use ACP3\Core\View\Block\Context\BlockContext;
use ACP3\Modules\ACP3\Polls\Model\Repository\PollAnswersRepository;
use ACP3\Modules\ACP3\Polls\Model\Repository\PollsRepository;

class PollDetailsBlock extends AbstractBlock
{
    /**
     * @var PollsRepository
     */
    private $pollRepository;
    /**
     * @var PollAnswersRepository
     */
    private $answerRepository;

    /**
     * PollResultBlock constructor.
     * @param BlockContext $context
     * @param PollsRepository $pollRepository
     * @param PollAnswersRepository $answerRepository
     */
    public function __construct(
        BlockContext $context,
        PollsRepository $pollRepository,
        PollAnswersRepository $answerRepository
    ) {
        parent::__construct($context);

        $this->pollRepository = $pollRepository;
        $this->answerRepository = $answerRepository;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $question = $this->pollRepository->getOneByIdWithTotalVotes($this->getData()['poll_id']);
        $answers = $this->answerRepository->getAnswersWithVotesByPollId($this->getData()['poll_id']);
        $totalVotes = $question['total_votes'];

        $cAnswers = \count($answers);
        for ($i = 0; $i < $cAnswers; ++$i) {
            $answers[$i]['percent'] = $totalVotes > 0 ? \round(100 * $answers[$i]['votes'] / $totalVotes, 2) : '0';
        }

        return [
            'question' => $question['title'],
            'answers' => $answers,
            'total_votes' => $totalVotes,
        ];
    }
}
