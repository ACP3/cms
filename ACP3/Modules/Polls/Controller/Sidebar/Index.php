<?php

namespace ACP3\Modules\Polls\Controller\Sidebar;

use ACP3\Core;
use ACP3\Modules\Polls;

/**
 * Description of PollsFrontend
 *
 * @author Tino Goratsch
 */
class Index extends Core\Modules\Controller\Sidebar
{

    /**
     *
     * @var Polls\Model
     */
    protected $model;

    protected function _init()
    {
        $this->model = new Polls\Model($this->db, $this->lang);
    }

    public function actionIndex()
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
                $this->setLayout('Polls/Sidebar/index.result.tpl');
            } else {
                $this->view->assign('sidebar_poll_answers', $answers);
                $this->setLayout('Polls/Sidebar/index.vote.tpl');
            }
        } else {
            $this->setLayout('Polls/Sidebar/index.vote.tpl');
        }
    }
}