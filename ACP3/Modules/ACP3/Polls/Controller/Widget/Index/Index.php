<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Polls\Controller\Widget\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Polls;

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
     * @var Core\View\Block\FormBlockInterface
     */
    private $formBlock;
    /**
     * @var Core\View\Block\BlockInterface
     */
    private $block;

    /**
     * @param \ACP3\Core\Controller\Context\WidgetContext $context
     * @param Core\View\Block\FormBlockInterface $formBlock
     * @param Core\View\Block\BlockInterface $block
     * @param Core\Date $date
     * @param \ACP3\Modules\ACP3\Polls\Model\Repository\PollRepository $pollRepository
     * @param \ACP3\Modules\ACP3\Polls\Model\Repository\AnswerRepository $answerRepository
     */
    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Core\View\Block\FormBlockInterface $formBlock,
        Core\View\Block\BlockInterface $block,
        Core\Date $date,
        Polls\Model\Repository\PollRepository $pollRepository,
        Polls\Model\Repository\AnswerRepository $answerRepository
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->pollRepository = $pollRepository;
        $this->answerRepository = $answerRepository;
        $this->formBlock = $formBlock;
        $this->block = $block;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $poll = $this->pollRepository->getLatestPoll(
            $this->date->getCurrentDateTime(),
            $this->user->getUserId(),
            $this->request->getSymfonyRequest()->getClientIp()
        );
        $answers = [];

        $this->setTemplate('Polls/Widget/index.vote.tpl');

        if (!empty($poll)) {
            $answers = $this->answerRepository->getAnswersWithVotesByPollId($poll['id']);

            if ($poll['has_voted'] == 1) {
                $this->setTemplate('Polls/Widget/index.result.tpl');

                return $this->block
                    ->setData(['poll_id' => $poll['id']])
                    ->render();
            }
        }

        return $this->formBlock
            ->setData(['poll' => $poll, 'answers' => $answers])
            ->render();
    }
}
