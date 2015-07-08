<?php

namespace ACP3\Modules\ACP3\Polls\Controller\Sidebar;

use ACP3\Core;
use ACP3\Modules\ACP3\Polls;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Polls\Controller\Sidebar
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

    /**
     * @param \ACP3\Core\Modules\Controller\Context $context
     * @param Core\Date $date
     * @param Polls\Model $pollsModel
     */
    public function __construct(
        Core\Modules\Controller\Context $context,
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
                $query = $this->pollsModel->getVotesByUserId($poll['id'], $this->auth->getUserId(), $this->request->getServer()->get('REMOTE_ADDR', '')); // Check, whether the logged user has already voted
            } else {
                $query = $this->pollsModel->getVotesByIpAddress($poll['id'], $this->request->getServer()->get('REMOTE_ADDR', '')); // For guest users check against the ip address
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
                $this->setTemplate('Polls/Sidebar/index.result.tpl');
                return;
            } else {
                $this->view->assign('sidebar_poll_answers', $answers);
            }
        }

        $this->setTemplate('Polls/Sidebar/index.vote.tpl');
    }
}
