<?php

namespace ACP3\Modules\ACP3\Polls\Controller;

use ACP3\Core;
use ACP3\Core\Modules\FrontendController;
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
     * @var Polls\Model
     */
    protected $pollsModel;

    /**
     * @param \ACP3\Core\Modules\Controller\FrontendContext $context
     * @param Core\Date                                     $date
     * @param Polls\Model                                   $pollsModel
     */
    public function __construct(
        Core\Modules\Controller\FrontendContext $context,
        Core\Date $date,
        Polls\Model $pollsModel)
    {
        parent::__construct($context);

        $this->date = $date;
        $this->pollsModel = $pollsModel;
    }

    public function actionIndex()
    {
        $polls = $this->pollsModel->getAll($this->date->getCurrentDateTime());
        $c_polls = count($polls);

        if ($c_polls > 0) {
            for ($i = 0; $i < $c_polls; ++$i) {
                if ($this->auth->isUser() === true) {
                    $query = $this->pollsModel->getVotesByUserId($polls[$i]['id'], $this->auth->getUserId(), $this->request->getServer()->get('REMOTE_ADDR', '')); // Check, whether the logged user has already voted
                } else {
                    $query = $this->pollsModel->getVotesByIpAddress($polls[$i]['id'], $this->request->getServer()->get('REMOTE_ADDR', '')); // For guest users check against the ip address
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

    public function actionResult()
    {
        if ($this->get('core.validator.rules.misc')->isNumber($this->request->getParameters()->get('id')) === true &&
            $this->pollsModel->pollExists($this->request->getParameters()->get('id'), $this->date->getCurrentDateTime()) === true
        ) {
            $question = $this->pollsModel->getOneByIdWithTotalVotes($this->request->getParameters()->get('id'));
            $answers = $this->pollsModel->getAnswersByPollId($this->request->getParameters()->get('id'));
            $c_answers = count($answers);
            $totalVotes = $question['total_votes'];

            for ($i = 0; $i < $c_answers; ++$i) {
                $answers[$i]['percent'] = $totalVotes > '0' ? round(100 * $answers[$i]['votes'] / $totalVotes, 2) : '0';
            }
            $this->view->assign('question', $question['title']);
            $this->view->assign('answers', $answers);
            $this->view->assign('total_votes', $totalVotes);
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionVote()
    {
        $time = $this->date->getCurrentDateTime();
        if ($this->get('core.validator.rules.misc')->isNumber($this->request->getParameters()->get('id')) === true &&
            $this->pollsModel->pollExists($this->request->getParameters()->get('id'), $time, is_array($this->request->getPost()->get('answer'))) === true
        ) {
            // Wenn abgestimmt wurde
            if (is_array($this->request->getPost()->get('answer')) === true || $this->get('core.validator.rules.misc')->isNumber($this->request->getPost()->get('answer')) === true) {
                $this->_votePost($this->request->getPost()->getAll(), $time);
            } else {
                $poll = $this->pollsModel->getOneById($this->request->getParameters()->get('id'));

                $this->view->assign('question', $poll['title']);
                $this->view->assign('multiple', $poll['multiple']);
                $this->view->assign('answers', $this->pollsModel->getAnswersById($this->request->getParameters()->get('id')));
            }
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    /**
     * @param array  $formData
     * @param string $time
     */
    protected function _votePost(array $formData, $time)
    {
        $ip = $this->request->getServer()->get('REMOTE_ADDR', '');
        $answers = $formData['answer'];

        if ($this->auth->isUser() === true) {
            $query = $this->pollsModel->getVotesByUserId($this->request->getParameters()->get('id'), $this->auth->getUserId(), $ip); // Check, whether the logged user has already voted
        } else {
            $query = $this->pollsModel->getVotesByIpAddress($this->request->getParameters()->get('id'), $ip); // For guest users check against the ip address
        }

        $bool = false;
        if ($query == 0) {
            $userId = $this->auth->isUser() ? $this->auth->getUserId() : 0;

            // Multiple Answers
            if (is_array($answers) === false) {
                $answers = [$answers];
            }

            foreach ($answers as $answer) {
                if ($this->get('core.validator.rules.misc')->isNumber($answer) === true) {
                    $insertValues = [
                        'poll_id' => $this->request->getParameters()->get('id'),
                        'answer_id' => $answer,
                        'user_id' => $userId,
                        'ip' => $ip,
                        'time' => $time,
                    ];
                    $bool = $this->pollsModel->insert($insertValues, Polls\Model::TABLE_NAME_VOTES);
                }
            }
            $text = $bool !== false ? $this->lang->t('polls', 'poll_success') : $this->lang->t('polls', 'poll_error');
        } else {
            $text = $this->lang->t('polls', 'already_voted');
        }

        $this->redirectMessages()->setMessage($bool, $text, 'polls/index/result/id_' . $this->request->getParameters()->get('id'));
    }
}
