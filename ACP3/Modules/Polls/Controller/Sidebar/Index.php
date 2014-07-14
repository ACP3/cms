<?php

namespace ACP3\Modules\Polls\Controller\Sidebar;

use ACP3\Core;
use ACP3\Modules\Polls;

/**
 * Class Index
 * @package ACP3\Modules\Polls\Controller\Sidebar
 */
class Index extends Core\Modules\Controller
{

    /**
     * @var Core\Date
     */
    protected $date;
    /**
     * @var Polls\Model
     */
    protected $pollsModel;

    public function __construct(
        Core\Context $context,
        Core\Date $date,
        Polls\Model $pollsModel)
    {
       parent::__construct($context);

        $this->date = $date;
        $this->pollsModel = $pollsModel;
    }

    public function actionIndex()
    {
        $poll = $this->pollsModel->getLatestPoll($this->date->getCurrentDateTime());

        if (!empty($poll)) {
            $answers = $this->pollsModel->getAnswersByPollId($poll['id']);

            $this->view->assign('sidebar_polls', $poll);

            if ($this->auth->isUser() === true) {
                $query = $this->pollsModel->getVotesByUserId($poll['id'], $this->auth->getUserId(), $_SERVER['REMOTE_ADDR']); // Check, whether the logged user has already voted
            } else {
                $query = $this->pollsModel->getVotesByIpAddress($poll['id'], $_SERVER['REMOTE_ADDR']); // For guest users check against the ip address
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