<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Polls\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Polls;

/**
 * Class Result
 * @package ACP3\Modules\ACP3\Polls\Controller\Frontend\Index
 */
class Result extends Core\Modules\FrontendController
{
    /**
     * @var Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\Polls\Model\PollRepository
     */
    protected $pollRepository;
    /**
     * @var \ACP3\Modules\ACP3\Polls\Model\AnswerRepository
     */
    protected $answerRepository;

    /**
     * Result constructor.
     *
     * @param \ACP3\Core\Modules\Controller\FrontendContext   $context
     * @param \ACP3\Core\Date                                 $date
     * @param \ACP3\Modules\ACP3\Polls\Model\PollRepository   $pollRepository
     * @param \ACP3\Modules\ACP3\Polls\Model\AnswerRepository $answerRepository
     */
    public function __construct(
        Core\Modules\Controller\FrontendContext $context,
        Core\Date $date,
        Polls\Model\PollRepository $pollRepository,
        Polls\Model\AnswerRepository $answerRepository)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->pollRepository = $pollRepository;
        $this->answerRepository = $answerRepository;
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function execute($id)
    {
        if ($this->pollRepository->pollExists($id, $this->date->getCurrentDateTime()) === true) {
            $question = $this->pollRepository->getOneByIdWithTotalVotes($id);
            $answers = $this->answerRepository->getAnswersWithVotesByPollId($id);
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

        throw new Core\Exceptions\ResultNotExists();
    }
}
