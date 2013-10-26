<?php

namespace ACP3\Modules\Polls;

use ACP3\Core;

/**
 * Description of PollsFrontend
 *
 * @author Tino Goratsch
 */
class Frontend extends Core\Modules\Controller {

	public function __construct() {
		parent::__construct();
	}

	public function actionList() {
		$polls = $this->db->fetchAll('SELECT p.id, p.start, p.end, p.title, COUNT(pv.poll_id) AS votes FROM ' . DB_PRE . 'polls AS p LEFT JOIN ' . DB_PRE . 'poll_votes AS pv ON(p.id = pv.poll_id) WHERE p.start <= ? GROUP BY p.id ORDER BY p.start DESC, p.end DESC, p.id DESC', array($this->date->getCurrentDateTime()));
		$c_polls = count($polls);

		if ($c_polls > 0) {
			for ($i = 0; $i < $c_polls; ++$i) {
				// Überprüfen, ob der eingeloggte User schon abgestimmt hat
				if ($this->auth->isUser() === true) {
					$query = $this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'poll_votes WHERE poll_id = ? AND user_id = ?', array($polls[$i]['id'], $this->auth->getUserId()));
					// Überprüfung für Gäste
				} else {
					$query = $this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'poll_votes WHERE poll_id = ? AND ip = ?', array($polls[$i]['id'], $_SERVER['REMOTE_ADDR']));
				}

				if ($query != 0 ||
						$polls[$i]['start'] != $polls[$i]['end'] && $this->date->timestamp($polls[$i]['end']) <= $this->date->timestamp()) {
					$polls[$i]['link'] = 'result';
				} else {
					$polls[$i]['link'] = 'vote';
				}
				$polls[$i]['date'] = $polls[$i]['start'] == $polls[$i]['end'] ? '-' : $this->date->format($polls[$i]['end']);
			}
			$this->view->assign('polls', $polls);
		}
	}

	public function actionResult() {
		if (Core\Validate::isNumber($this->uri->id) === true &&
				$this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'polls WHERE id = ? AND start <= ?', array($this->uri->id, $this->date->getCurrentDateTime())) == 1) {
			Core\Functions::getRedirectMessage();

			$this->breadcrumb
					->append($this->lang->t('polls', 'polls'), $this->uri->route('polls'))
					->append($this->lang->t('polls', 'result'));

			$question = $this->db->fetchAssoc('SELECT p.title, COUNT(pv.poll_id) AS total_votes FROM ' . DB_PRE . 'polls AS p LEFT JOIN ' . DB_PRE . 'poll_votes AS pv ON(p.id = pv.poll_id) WHERE p.id = ?', array($this->uri->id));
			$answers = $this->db->fetchAll('SELECT pa.id, pa.text, COUNT(pv.answer_id) AS votes FROM ' . DB_PRE . 'poll_answers AS pa LEFT JOIN ' . DB_PRE . 'poll_votes AS pv ON(pa.id = pv.answer_id) WHERE pa.poll_id = ? GROUP BY pa.id ORDER BY pa.id ASC', array($this->uri->id));
			$c_answers = count($answers);
			$total_votes = $question['total_votes'];

			for ($i = 0; $i < $c_answers; ++$i) {
				$answers[$i]['percent'] = $total_votes > '0' ? round(100 * $answers[$i]['votes'] / $total_votes, 2) : '0';
			}
			$this->view->assign('question', $question['title']);
			$this->view->assign('answers', $answers);
			$this->view->assign('total_votes', $total_votes);
		} else {
			$this->uri->redirect('errors/404');
		}
	}

	public function actionSidebar() {
		$period = 'p.start = p.end AND p.start <= :time OR p.start != p.end AND :time BETWEEN p.start AND p.end';
		$poll = $this->db->fetchAssoc('SELECT p.id, p.title, p.multiple, COUNT(pv.poll_id) AS total_votes FROM ' . DB_PRE . 'polls AS p LEFT JOIN ' . DB_PRE . 'poll_votes AS pv ON(p.id = pv.poll_id) WHERE ' . $period . ' GROUP BY p.id ORDER BY p.start DESC', array('time' => $this->date->getCurrentDateTime()));

		if (!empty($poll)) {
			$answers = $this->db->fetchAll('SELECT pa.id, pa.text, COUNT(pv.answer_id) AS votes FROM ' . DB_PRE . 'poll_answers AS pa LEFT JOIN ' . DB_PRE . 'poll_votes AS pv ON(pa.id = pv.answer_id) WHERE pa.poll_id = ? GROUP BY pa.id ORDER BY pa.id ASC', array($poll['id']));
			$c_answers = count($answers);

			$this->view->assign('sidebar_polls', $poll);

			// Überprüfen, ob der eingeloggte User schon abgestimmt hat
			if ($this->auth->isUser() === true) {
				$alreadyVoted = $this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'poll_votes WHERE poll_id = ? AND user_id = ?', array($poll['id'], $this->auth->getUserId()));
				// Überprüfung für Gäste
			} else {
				$alreadyVoted = $this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'poll_votes WHERE poll_id = ? AND ip = ?', array($poll['id'], $_SERVER['REMOTE_ADDR']));
			}

			if ($alreadyVoted > 0) {
				$total_votes = $poll['total_votes'];

				for ($i = 0; $i < $c_answers; ++$i) {
					$votes = $answers[$i]['votes'];
					$answers[$i]['votes'] = ($votes > 1) ? sprintf($this->lang->t('polls', 'number_of_votes'), $votes) : $this->lang->t('polls', ($votes == 1 ? 'one_vote' : 'no_votes'));
					$answers[$i]['percent'] = $total_votes > 0 ? round(100 * $votes / $total_votes, 2) : '0';
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

	public function actionVote() {
		$time = $this->date->getCurrentDateTime();
		$period = ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)';
		$multiple = !empty($_POST['answer']) && is_array($_POST['answer']) ? ' AND multiple = \'1\'' : '';

		if (Core\Validate::isNumber($this->uri->id) === true &&
				$this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'polls WHERE id = :id' . $period . $multiple, array('id' => $this->uri->id, 'time' => $time)) == 1) {
			// Brotkrümelspur
			$this->breadcrumb
					->append($this->lang->t('polls', 'polls'), $this->uri->route('polls'))
					->append($this->lang->t('polls', 'vote'));

			// Wenn abgestimmt wurde
			if (!empty($_POST['answer']) && (is_array($_POST['answer']) === true || Core\Validate::isNumber($_POST['answer']) === true)) {
				$ip = $_SERVER['REMOTE_ADDR'];
				$answers = $_POST['answer'];

				// Überprüfen, ob der eingeloggte User schon abgestimmt hat
				if ($this->auth->isUser() === true) {
					$query = $this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'poll_votes WHERE poll_id = ? AND user_id = ?', array($this->uri->id, $this->auth->getUserId()));
					// Überprüfung für Gäste
				} else {
					$query = $this->db->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'poll_votes WHERE poll_id = ? AND ip = ?', array($this->uri->id, $ip));
				}

				if ($query == 0) {
					$user_id = $this->auth->isUser() ? $this->auth->getUserId() : 0;

					if (is_array($answers) === true) {
						foreach ($answers as $answer) {
							if (Core\Validate::isNumber($answer) === true) {
								$insert_values = array(
									'poll_id' => $this->uri->id,
									'answer_id' => $answer,
									'user_id' => $user_id,
									'ip' => $ip,
									'time' => $time,
								);
								$this->db->insert(DB_PRE . 'poll_votes', $insert_values);
							}
						}
						$bool = true;
					} else {
						$insert_values = array(
							'poll_id' => $this->uri->id,
							'answer_id' => $answers,
							'user_id' => $user_id,
							'ip' => $ip,
							'time' => $time,
						);
						$bool = $this->db->insert(DB_PRE . 'poll_votes', $insert_values);
					}
					$text = $bool !== false ? $this->lang->t('polls', 'poll_success') : $this->lang->t('polls', 'poll_error');
				} else {
					$text = $this->lang->t('polls', 'already_voted');
				}
				Core\Functions::setRedirectMessage($bool, $text, 'polls/result/id_' . $this->uri->id);
			} else {
				$poll = $this->db->fetchAssoc('SELECT title, multiple FROM ' . DB_PRE . 'polls WHERE id = ?', array($this->uri->id));
				$answers = $this->db->fetchAll('SELECT id, text FROM ' . DB_PRE . 'poll_answers WHERE poll_id = ? ORDER BY id ASC', array($this->uri->id));

				$this->view->assign('question', $poll['title']);
				$this->view->assign('multiple', $poll['multiple']);
				$this->view->assign('answers', $answers);
			}
		} else {
			$this->uri->redirect('errors/404');
		}
	}

}