<?php

namespace ACP3\Modules\Polls;

use ACP3\Core;

/**
 * Description of PollsFrontend
 *
 * @author Tino
 */
class PollsFrontend extends Core\ModuleController {

	public function __construct($injector) {
		parent::__construct($injector);
	}

	public function actionList() {
		$polls = $this->injector['Db']->fetchAll('SELECT p.id, p.start, p.end, p.title, COUNT(pv.poll_id) AS votes FROM ' . DB_PRE . 'polls AS p LEFT JOIN ' . DB_PRE . 'poll_votes AS pv ON(p.id = pv.poll_id) WHERE p.start <= ? GROUP BY p.id ORDER BY p.start DESC, p.end DESC, p.id DESC', array($this->injector['Date']->getCurrentDateTime()));
		$c_polls = count($polls);

		if ($c_polls > 0) {
			for ($i = 0; $i < $c_polls; ++$i) {
				// Überprüfen, ob der eingeloggte User schon abgestimmt hat
				if ($this->injector['Auth']->isUser() === true) {
					$query = $this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'poll_votes WHERE poll_id = ? AND user_id = ?', array($polls[$i]['id'], $this->injector['Auth']->getUserId()));
					// Überprüfung für Gäste
				} else {
					$query = $this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'poll_votes WHERE poll_id = ? AND ip = ?', array($polls[$i]['id'], $_SERVER['REMOTE_ADDR']));
				}

				if ($query != 0 ||
						$polls[$i]['start'] != $polls[$i]['end'] && $this->injector['Date']->timestamp($polls[$i]['end']) <= $this->injector['Date']->timestamp()) {
					$polls[$i]['link'] = 'result';
				} else {
					$polls[$i]['link'] = 'vote';
				}
				$polls[$i]['date'] = $polls[$i]['start'] == $polls[$i]['end'] ? '-' : $this->injector['Date']->format($polls[$i]['end']);
			}
			$this->injector['View']->assign('polls', $polls);
		}
	}

	public function actionResult() {
		if (Core\Validate::isNumber($this->injector['URI']->id) === true &&
				$this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'polls WHERE id = ? AND start <= ?', array($this->injector['URI']->id, $this->injector['Date']->getCurrentDateTime())) == 1) {
			Core\Functions::getRedirectMessage();

			$this->injector['Breadcrumb']
					->append($this->injector['Lang']->t('polls', 'polls'), $this->injector['URI']->route('polls'))
					->append($this->injector['Lang']->t('polls', 'result'));

			$question = $this->injector['Db']->fetchAssoc('SELECT p.title, COUNT(pv.poll_id) AS total_votes FROM ' . DB_PRE . 'polls AS p LEFT JOIN ' . DB_PRE . 'poll_votes AS pv ON(p.id = pv.poll_id) WHERE p.id = ?', array($this->injector['URI']->id));
			$answers = $this->injector['Db']->fetchAll('SELECT pa.id, pa.text, COUNT(pv.answer_id) AS votes FROM ' . DB_PRE . 'poll_answers AS pa LEFT JOIN ' . DB_PRE . 'poll_votes AS pv ON(pa.id = pv.answer_id) WHERE pa.poll_id = ? GROUP BY pa.id ORDER BY pa.id ASC', array($this->injector['URI']->id));
			$c_answers = count($answers);
			$total_votes = $question['total_votes'];

			for ($i = 0; $i < $c_answers; ++$i) {
				$answers[$i]['percent'] = $total_votes > '0' ? round(100 * $answers[$i]['votes'] / $total_votes, 2) : '0';
			}
			$this->injector['View']->assign('question', $question['title']);
			$this->injector['View']->assign('answers', $answers);
			$this->injector['View']->assign('total_votes', $total_votes);
		} else {
			$this->injector['URI']->redirect('errors/404');
		}
	}

	public function actionSidebar() {
		$period = 'p.start = p.end AND p.start <= :time OR p.start != p.end AND :time BETWEEN p.start AND p.end';
		$poll = $this->injector['Db']->fetchAssoc('SELECT p.id, p.title, p.multiple, COUNT(pv.poll_id) AS total_votes FROM ' . DB_PRE . 'polls AS p LEFT JOIN ' . DB_PRE . 'poll_votes AS pv ON(p.id = pv.poll_id) WHERE ' . $period . ' GROUP BY p.id ORDER BY p.start DESC', array('time' => $this->injector['Date']->getCurrentDateTime()));

		if (!empty($poll)) {
			$answers = $this->injector['Db']->fetchAll('SELECT pa.id, pa.text, COUNT(pv.answer_id) AS votes FROM ' . DB_PRE . 'poll_answers AS pa LEFT JOIN ' . DB_PRE . 'poll_votes AS pv ON(pa.id = pv.answer_id) WHERE pa.poll_id = ? GROUP BY pa.id ORDER BY pa.id ASC', array($poll['id']));
			$c_answers = count($answers);

			$this->injector['View']->assign('sidebar_polls', $poll);

			// Überprüfen, ob der eingeloggte User schon abgestimmt hat
			if ($this->injector['Auth']->isUser() === true) {
				$alreadyVoted = $this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'poll_votes WHERE poll_id = ? AND user_id = ?', array($poll['id'], $this->injector['Auth']->getUserId()));
				// Überprüfung für Gäste
			} else {
				$alreadyVoted = $this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'poll_votes WHERE poll_id = ? AND ip = ?', array($poll['id'], $_SERVER['REMOTE_ADDR']));
			}

			if ($alreadyVoted > 0) {
				$total_votes = $poll['total_votes'];

				for ($i = 0; $i < $c_answers; ++$i) {
					$votes = $answers[$i]['votes'];
					$answers[$i]['votes'] = ($votes > 1) ? sprintf($this->injector['Lang']->t('polls', 'number_of_votes'), $votes) : $this->injector['Lang']->t('polls', ($votes == 1 ? 'one_vote' : 'no_votes'));
					$answers[$i]['percent'] = $total_votes > 0 ? round(100 * $votes / $total_votes, 2) : '0';
				}

				$this->injector['View']->assign('sidebar_poll_answers', $answers);
				$this->injector['View']->displayTemplate('polls/sidebar_result.tpl');
			} else {
				$this->injector['View']->assign('sidebar_poll_answers', $answers);
				$this->injector['View']->displayTemplate('polls/sidebar_vote.tpl');
			}
		} else {
			$this->injector['View']->displayTemplate('polls/sidebar_vote.tpl');
		}
	}

	public function actionVote() {
		$time = $this->injector['Date']->getCurrentDateTime();
		$period = ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)';
		$multiple = !empty($_POST['answer']) && is_array($_POST['answer']) ? ' AND multiple = \'1\'' : '';

		if (Core\Validate::isNumber($this->injector['URI']->id) === true &&
				$this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'polls WHERE id = :id' . $period . $multiple, array('id' => $this->injector['URI']->id, 'time' => $time)) == 1) {
			// Brotkrümelspur
			$this->injector['Breadcrumb']
					->append($this->injector['Lang']->t('polls', 'polls'), $this->injector['URI']->route('polls'))
					->append($this->injector['Lang']->t('polls', 'vote'));

			// Wenn abgestimmt wurde
			if (!empty($_POST['answer']) && (is_array($_POST['answer']) === true || Core\Validate::isNumber($_POST['answer']) === true)) {
				$ip = $_SERVER['REMOTE_ADDR'];
				$answers = $_POST['answer'];

				// Überprüfen, ob der eingeloggte User schon abgestimmt hat
				if ($this->injector['Auth']->isUser() === true) {
					$query = $this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'poll_votes WHERE poll_id = ? AND user_id = ?', array($this->injector['URI']->id, $this->injector['Auth']->getUserId()));
					// Überprüfung für Gäste
				} else {
					$query = $this->injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'poll_votes WHERE poll_id = ? AND ip = ?', array($this->injector['URI']->id, $ip));
				}

				if ($query == 0) {
					$user_id = $this->injector['Auth']->isUser() ? $this->injector['Auth']->getUserId() : 0;

					if (is_array($answers) === true) {
						foreach ($answers as $answer) {
							if (Core\Validate::isNumber($answer) === true) {
								$insert_values = array(
									'poll_id' => $this->injector['URI']->id,
									'answer_id' => $answer,
									'user_id' => $user_id,
									'ip' => $ip,
									'time' => $time,
								);
								$this->injector['Db']->insert(DB_PRE . 'poll_votes', $insert_values);
							}
						}
						$bool = true;
					} else {
						$insert_values = array(
							'poll_id' => $this->injector['URI']->id,
							'answer_id' => $answers,
							'user_id' => $user_id,
							'ip' => $ip,
							'time' => $time,
						);
						$bool = $this->injector['Db']->insert(DB_PRE . 'poll_votes', $insert_values);
					}
					$text = $bool !== false ? $this->injector['Lang']->t('polls', 'poll_success') : $this->injector['Lang']->t('polls', 'poll_error');
				} else {
					$text = $this->injector['Lang']->t('polls', 'already_voted');
				}
				Core\Functions::setRedirectMessage($bool, $text, 'polls/result/id_' . $this->injector['URI']->id);
			} else {
				$poll = $this->injector['Db']->fetchAssoc('SELECT title, multiple FROM ' . DB_PRE . 'polls WHERE id = ?', array($this->injector['URI']->id));
				$answers = $this->injector['Db']->fetchAll('SELECT id, text FROM ' . DB_PRE . 'poll_answers WHERE poll_id = ? ORDER BY id ASC', array($this->injector['URI']->id));

				$this->injector['View']->assign('question', $poll['title']);
				$this->injector['View']->assign('multiple', $poll['multiple']);
				$this->injector['View']->assign('answers', $answers);
			}
		} else {
			$this->injector['URI']->redirect('errors/404');
		}
	}

}