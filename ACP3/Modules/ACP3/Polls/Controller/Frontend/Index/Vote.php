<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
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
     * @var Polls\Model\VoteModel
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
     * @param Polls\Model\VoteModel $pollsModel
     * @param \ACP3\Modules\ACP3\Polls\Model\Repository\PollRepository $pollRepository
     * @param \ACP3\Modules\ACP3\Polls\Model\Repository\AnswerRepository $answerRepository
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Date $date,
        Polls\Validation\VoteValidation $voteValidation,
        Polls\Model\VoteModel $pollsModel,
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
     * @return array
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function execute($id)
    {
        $answer = $this->request->getPost()->get('answer');
        $time = $this->date->getCurrentDateTime();
        if ($this->pollRepository->pollExists($id, $time, is_array($answer)) === true) {
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
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost($id)
    {
        return $this->actionHelper->handlePostAction(
            function () use ($id) {
                $formData = $this->request->getPost()->all();
                $ipAddress = $this->request->getSymfonyRequest()->getClientIp();
                $time = $this->date->getCurrentDateTime();

                $this->voteValidation
                    ->setPollId($id)
                    ->setIpAddress($ipAddress)
                    ->validate($formData);

                $result = $this->pollsModel->vote($formData, $id, $ipAddress, $time);

                $text = $this->translator->t('polls', $result !== false ? 'poll_success' : 'poll_error');
                return $this->redirectMessages()->setMessage($result, $text, 'polls/index/result/id_' . $id);
            },
            'polls/index/vote/id_' . $id
        );
    }
}
