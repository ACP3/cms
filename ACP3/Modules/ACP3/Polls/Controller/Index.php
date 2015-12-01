<?php

namespace ACP3\Modules\ACP3\Polls\Controller;

use ACP3\Core;
use ACP3\Modules\ACP3\Polls;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Polls\Controller
 */
class Index extends Core\Modules\FrontendController
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

    public function actionIndex()
    {
        $polls = $this->pollRepository->getAll($this->date->getCurrentDateTime());
        $c_polls = count($polls);

        if ($c_polls > 0) {
            for ($i = 0; $i < $c_polls; ++$i) {
                if ($this->user->isAuthenticated() === true) {
                    $query = $this->voteRepository->getVotesByUserId($polls[$i]['id'], $this->user->getUserId(), $this->request->getServer()->get('REMOTE_ADDR', '')); // Check, whether the logged user has already voted
                } else {
                    $query = $this->voteRepository->getVotesByIpAddress($polls[$i]['id'], $this->request->getServer()->get('REMOTE_ADDR', '')); // For guest users check against the ip address
                }

                if ($query != 0 ||
                    $polls[$i]['start'] !== $polls[$i]['end'] && $this->date->timestamp($polls[$i]['end']) <= $this->date->timestamp()
                ) {
                    $polls[$i]['link'] = 'result';
                } else {
                    $polls[$i]['link'] = 'vote';
                }
            }
            $this->view->assign('polls', $polls);
        }
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionResult($id)
    {
        if ($this->pollRepository->pollExists($id, $this->date->getCurrentDateTime()) === true) {
            $question = $this->pollRepository->getOneByIdWithTotalVotes($id);
            $answers = $this->answerRepository->getAnswersWithVotesByPollId($id);
            $c_answers = count($answers);
            $totalVotes = $question['total_votes'];

            for ($i = 0; $i < $c_answers; ++$i) {
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

    /**
     * @param int       $id
     *
     * @param int|array $answer
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function actionVote($id, $answer)
    {
        $time = $this->date->getCurrentDateTime();
        if ($this->pollRepository->pollExists($id, $time, is_array($answer)) === true) {
            // Wenn abgestimmt wurde
            if (!empty($answer) || is_array($answer) === true) {
                return $this->_votePost($this->request->getPost()->all(), $time, $id);
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
    protected function _votePost(array $formData, $time, $id)
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
                if ($this->validator->is(Core\Validator\ValidationRules\IntegerValidationRule::NAME, $answer) === true) {
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
            $text = $bool !== false ? $this->lang->t('polls', 'poll_success') : $this->lang->t('polls', 'poll_error');
        } else {
            $text = $this->lang->t('polls', 'already_voted');
        }

        return $this->redirectMessages()->setMessage($bool, $text, 'polls/index/result/id_' . $id);
    }
}
