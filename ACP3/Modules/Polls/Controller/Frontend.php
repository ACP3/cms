<?php

namespace ACP3\Modules\Polls\Controller;

use ACP3\Core;
use ACP3\Modules\Polls;

/**
 * Description of PollsFrontend
 *
 * @author Tino Goratsch
 */
class Frontend extends Core\Modules\Controller
{

    /**
     *
     * @var Model
     */
    protected $model;

    public function __construct(
        Core\Auth $auth,
        Core\Breadcrumb $breadcrumb,
        Core\Date $date,
        \Doctrine\DBAL\Connection $db,
        Core\Lang $lang,
        Core\Session $session,
        Core\URI $uri,
        Core\View $view,
        Core\SEO $seo)
    {
        parent::__construct($auth, $breadcrumb, $date, $db, $lang, $session, $uri, $view, $seo);

        $this->model = new Polls\Model($db, $lang);
    }

    public function actionList()
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

            $this->breadcrumb
                ->append($this->lang->t('polls', 'polls'), $this->uri->route('polls'))
                ->append($this->lang->t('polls', 'result'));

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
            $this->uri->redirect('errors/404');
        }
    }

    public function actionSidebar()
    {
        $poll = $this->model->getLatestPoll($this->date->getCurrentDateTime());

        if (!empty($poll)) {
            $answers = $this->model->getAnswersByPollId($poll['id']);

            $this->view->assign('sidebar_polls', $poll);

            if ($this->auth->isUser() === true) {
                $query = $this->model->getVotesByUserId($poll['id'], $this->auth->getUserId(), $_SERVER['REMOTE_ADDR']); // Check, whether the logged user has already voted
            } else {
                $query = $this->model->getVotesByIpAddress($poll['id'], $_SERVER['REMOTE_ADDR']); // For guest users check against the ip address
            }

            if ($query > 0) {
                $totalVotes = $poll['total_votes'];

                $c_answers = count($answers);
                for ($i = 0; $i < $c_answers; ++$i) {
                    $votes = $answers[$i]['votes'];
                    $answers[$i]['votes'] = ($votes > 1) ? sprintf($this->lang->t('polls', 'number_of_votes'), $votes) : $this->lang->t('polls', ($votes == 1 ? 'one_vote' : 'no_votes'));
                    $answers[$i]['percent'] = $totalVotes > 0 ? round(100 * $votes / $totalVotes, 2) : '0';
                }

                $this->view->assign('sidebar_poll_answers', $answers);
                $this->view->displayTemplate('polls/sidebar_result.tpl');
            } else {
                $this->view->assign('sidebar_poll_answers', $answers);
                $this->view->displayTemplate('polls/sidebar_vote.tpl');
            }
        } else {
            $this->view->displayTemplate('polls/sidebar_vote.tpl');
        }
    }

    public function actionVote()
    {
        $time = $this->date->getCurrentDateTime();
        if (Core\Validate::isNumber($this->uri->id) === true &&
            $this->model->pollExists($this->uri->id, $time, !empty($_POST['answer']) && is_array($_POST['answer'])) === true) {
            // Brotkrümelspur
            $this->breadcrumb
                ->append($this->lang->t('polls', 'polls'), $this->uri->route('polls'))
                ->append($this->lang->t('polls', 'vote'));

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

                Core\Functions::setRedirectMessage($bool, $text, 'polls/result/id_' . $this->uri->id);
            } else {
                $poll = $this->model->getOneById($this->uri->id);

                $this->view->assign('question', $poll['title']);
                $this->view->assign('multiple', $poll['multiple']);
                $this->view->assign('answers', $this->model->getAnswersById($this->uri->id));
            }
        } else {
            $this->uri->redirect('errors/404');
        }
    }

}