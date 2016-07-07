<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Polls\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Polls;

/**
 * Class Vote
 * @package ACP3\Modules\ACP3\Polls\Controller\Frontend\Index
 */
class Vote extends Core\Controller\AbstractFrontendAction
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
     * @var Polls\Model\PollsModel
     */
    protected $pollsModel;
    /**
     * @var Polls\Validation\VoteValidation
     */
    protected $voteValidation;

    /**
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\Date $date
     * @param Polls\Validation\VoteValidation $voteValidation
     * @param Polls\Model\PollsModel $pollsModel
     * @param \ACP3\Modules\ACP3\Polls\Model\Repository\PollRepository $pollRepository
     * @param \ACP3\Modules\ACP3\Polls\Model\Repository\AnswerRepository $answerRepository
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Date $date,
        Polls\Validation\VoteValidation $voteValidation,
        Polls\Model\PollsModel $pollsModel,
        Polls\Model\Repository\PollRepository $pollRepository,
        Polls\Model\Repository\AnswerRepository $answerRepository
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->voteValidation = $voteValidation;
        $this->pollsModel = $pollsModel;
        $this->pollRepository = $pollRepository;
        $this->answerRepository = $answerRepository;
    }

    /**
     * @param int $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        $answer = $this->request->getPost()->get('answer');
        $time = $this->date->getCurrentDateTime();
        if ($this->pollRepository->pollExists($id, $time, is_array($answer)) === true) {
            if (!empty($answer) || is_array($answer) === true) {
                return $this->executePost($this->request->getPost()->all(), $time, $id);
            }

            $poll = $this->pollRepository->getOneById($id);

            return [
                'question' => $poll['title'],
                'multiple' => $poll['multiple'],
                'answers' => $this->answerRepository->getAnswersByPollId($id)
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param array $formData
     * @param string $time
     * @param int $pollId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, $time, $pollId)
    {
        return $this->actionHelper->handlePostAction(
            function () use ($formData, $time, $pollId) {
                $ipAddress = $this->request->getServer()->get('REMOTE_ADDR', '');

                $this->voteValidation
                    ->setPollId($pollId)
                    ->setIpAddress($ipAddress)
                    ->validate($formData);

                $result = $this->pollsModel->vote($formData, $pollId, $ipAddress, $time);

                $text = $this->translator->t('polls', $result !== false ? 'poll_success' : 'poll_error');
                return $this->redirectMessages()->setMessage($result, $text, 'polls/index/result/id_' . $pollId);
            },
            'polls/index/vote/id_' . $pollId
        );
    }
}
