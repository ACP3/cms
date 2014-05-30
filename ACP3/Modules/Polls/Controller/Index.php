<?php

namespace ACP3\Modules\Polls\Controller;

use ACP3\Core;
use ACP3\Modules\Polls;

/**
 * Description of PollsFrontend
 *
 * @author Tino Goratsch
 */
class Index extends Core\Modules\Controller
{

    /**
     *
     * @var Polls\Model
     */
    protected $model;

    public function preDispatch()
    {
        parent::preDispatch();

        $this->model = new Polls\Model($this->db, $this->lang);
    }

    public function actionIndex()
    {
        $polls = $this->model->getAll($this->date->getCurrentDateTime());
        $c_polls = count($polls);

        if ($c_polls > 0) {
            for ($i = 0; $i < $c_polls; ++$i) {
                if ($this->auth->isUser() === true) {
                    $query = $this->model->getVotesByUserId($polls[$i]['id'], $this->auth->getUserId(), $_SERVER['REMOTE_ADDR']); // Check, whether the logged user has already voted
                } else {
                    $query = $this->model->getVotesByIpAddress($polls[$i]['id'], $_SERVER['REMOTE_ADDR']); // For guest users check against the ip address
                }

                if ($query != 0 ||
                    $polls[$i]['start'] !== $polls[$i]['end'] && $this->date->timestamp($polls[$i]['end']) <= $this->date->timestamp()
                ) {
                    $polls[$i]['link'] = 'result';
                } else {
                    $polls[$i]['link'] = 'vote';
                }
                $polls[$i]['date'] = ($polls[$i]['start'] === $polls[$i]['end']) ? '-' : $this->date->format($polls[$i]['end']);
            }
            $this->view->assign('polls', $polls);
        }
    }

    public function actionResult()
    {
        if (Core\Validate::isNumber($this->uri->id) === true &&
            $this->model->pollExists($this->uri->id, $this->date->getCurrentDateTime()) === true) {
            Core\Functions::getRedirectMessage();

            $question = $this->model->getOneByIdWithTotalVotes($this->uri->id);
            $answers = $this->model->getAnswersByPollId($this->uri->id);
            $c_answers = count($answers);
            $totalVotes = $question['total_votes'];

            for ($i = 0; $i < $c_answers; ++$i) {
                $answers[$i]['percent'] = $totalVotes > '0' ? round(100 * $answers[$i]['votes'] / $totalVotes, 2) : '0';
            }
            $this->view->assign('question', $question['title']);
            $this->view->assign('answers', $answers);
            $this->view->assign('total_votes', $totalVotes);
        } else {
            $this->uri->redirect('errors/index/404');
        }
    }

    public function actionVote()
    {
        $time = $this->date->getCurrentDateTime();
        if (Core\Validate::isNumber($this->uri->id) === true &&
            $this->model->pollExists($this->uri->id, $time, !empty($_POST['answer']) && is_array($_POST['answer'])) === true) {

            // Wenn abgestimmt wurde
            if (!empty($_POST['answer']) && (is_array($_POST['answer']) === true || Core\Validate::isNumber($_POST['answer']) === true)) {
                $ip = $_SERVER['REMOTE_ADDR'];
                $answers = $_POST['answer'];

                if ($this->auth->isUser() === true) {
                    $query = $this->model->getVotesByUserId($this->uri->id, $this->auth->getUserId(), $ip); // Check, whether the logged user has already voted
                } else {
                    $query = $this->model->getVotesByIpAddress($this->uri->id, $ip); // For guest users check against the ip address
                }

                $bool = false;
                if ($query == 0) {
                    $userId = $this->auth->isUser() ? $this->auth->getUserId() : 0;

                    // Multiple Answers
                    if (is_array($answers) === false) {
                        $answers = array($answers);
                    }

                    foreach ($answers as $answer) {
                        if (Core\Validate::isNumber($answer) === true) {
                            $insertValues = array(
                                'poll_id' => $this->uri->id,
                                'answer_id' => $answer,
                                'user_id' => $userId,
                                'ip' => $ip,
                                'time' => $time,
                            );
                            $bool = $this->model->insert($insertValues, Polls\Model::TABLE_NAME_VOTES);
                        }
                    }
                    $text = $bool !== false ? $this->lang->t('polls', 'poll_success') : $this->lang->t('polls', 'poll_error');
                } else {
                    $text = $this->lang->t('polls', 'already_voted');
                }

                Core\Functions::setRedirectMessage($bool, $text, 'polls/index/result/id_' . $this->uri->id);
            } else {
                $poll = $this->model->getOneById($this->uri->id);

                $this->view->assign('question', $poll['title']);
                $this->view->assign('multiple', $poll['multiple']);
                $this->view->assign('answers', $this->model->getAnswersById($this->uri->id));
            }
        } else {
            $this->uri->redirect('errors/index/404');
        }
    }

}