<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Polls\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Polls;

/**
 * Class Vote
 * @package ACP3\Modules\ACP3\Polls\Controller\Frontend\Index
 */
class Vote extends Core\Modules\FrontendController
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
     * @var \ACP3\Modules\ACP3\Polls\Model\VoteRepository
     */
    protected $voteRepository;

    /**
     * @param \ACP3\Core\Modules\Controller\FrontendContext   $context
     * @param Core\Date                                       $date
     * @param \ACP3\Modules\ACP3\Polls\Model\PollRepository   $pollRepository
     * @param \ACP3\Modules\ACP3\Polls\Model\AnswerRepository $answerRepository
     * @param \ACP3\Modules\ACP3\Polls\Model\VoteRepository   $voteRepository
     */
    public function __construct(
        Core\Modules\Controller\FrontendContext $context,
        Core\Date $date,
        Polls\Model\PollRepository $pollRepository,
        Polls\Model\AnswerRepository $answerRepository,
        Polls\Model\VoteRepository $voteRepository)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->pollRepository = $pollRepository;
        $this->answerRepository = $answerRepository;
        $this->voteRepository = $voteRepository;
    }

    /**
     * @param int       $id
     *
     * @param int|array $answer
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function execute($id, $answer)
    {
        $time = $this->date->getCurrentDateTime();
        if ($this->pollRepository->pollExists($id, $time, is_array($answer)) === true) {
            // Wenn abgestimmt wurde
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

        throw new Core\Exceptions\ResultNotExists();
    }

    /**
     * @param array  $formData
     * @param string $time
     * @param int    $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, $time, $id)
    {
        $ip = $this->request->getServer()->get('REMOTE_ADDR', '');
        $answers = $formData['answer'];

        if ($this->user->isAuthenticated() === true) {
            $query = $this->voteRepository->getVotesByUserId($id, $this->user->getUserId(), $ip); // Check, whether the logged user has already voted
        } else {
            $query = $this->voteRepository->getVotesByIpAddress($id, $ip); // For guest users check against the ip address
        }

        $bool = false;
        if ($query == 0) {
            $userId = $this->user->isAuthenticated() ? $this->user->getUserId() : null;

            // Multiple Answers
            if (is_array($answers) === false) {
                $answers = [$answers];
            }

            foreach ($answers as $answer) {
                if ($this->validator->is(Core\Validation\ValidationRules\IntegerValidationRule::class, $answer) === true) {
                    $insertValues = [
                        'poll_id' => $id,
                        'answer_id' => $answer,
                        'user_id' => $userId,
                        'ip' => $ip,
                        'time' => $time,
                    ];
                    $bool = $this->voteRepository->insert($insertValues);
                }
            }
            $text = $bool !== false ? $this->translator->t('polls', 'poll_success') : $this->translator->t('polls',
                'poll_error');
        } else {
            $text = $this->translator->t('polls', 'already_voted');
        }

        return $this->redirectMessages()->setMessage($bool, $text, 'polls/index/result/id_' . $id);
    }
}
