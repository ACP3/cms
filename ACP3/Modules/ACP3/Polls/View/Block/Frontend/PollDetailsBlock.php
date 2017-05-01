<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Polls\View\Block\Frontend;


use ACP3\Core\View\Block\AbstractBlock;
use ACP3\Core\View\Block\Context\BlockContext;
use ACP3\Modules\ACP3\Polls\Model\Repository\AnswerRepository;
use ACP3\Modules\ACP3\Polls\Model\Repository\PollRepository;

class PollDetailsBlock extends AbstractBlock
{
    /**
     * @var PollRepository
     */
    private $pollRepository;
    /**
     * @var AnswerRepository
     */
    private $answerRepository;

    /**
     * PollResultBlock constructor.
     * @param BlockContext $context
     * @param PollRepository $pollRepository
     * @param AnswerRepository $answerRepository
     */
    public function __construct(
        BlockContext $context,
        PollRepository $pollRepository,
        AnswerRepository $answerRepository
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
        $cAnswers = count($answers);
        $totalVotes = $question['total_votes'];

        for ($i = 0; $i < $cAnswers; ++$i) {
            $answers[$i]['percent'] = $totalVotes > 0 ? round(100 * $answers[$i]['votes'] / $totalVotes, 2) : '0';
        }

        return [
            'question' => $question['title'],
            'answers' => $answers,
            'total_votes' => $totalVotes
        ];
    }
}
