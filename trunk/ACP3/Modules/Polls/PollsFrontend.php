<?php

namespace ACP3\Modules\Polls;

use ACP3\Core;

/**
 * Description of PollsFrontend
 *
 * @author Tino
 */
class PollsFrontend extends Core\ModuleController {

	public function actionList() {
		$polls = Core\Registry::get('Db')->fetchAll('SELECT p.id, p.start, p.end, p.title, COUNT(pv.poll_id) AS votes FROM ' . DB_PRE . 'polls AS p LEFT JOIN ' . DB_PRE . 'poll_votes AS pv ON(p.id = pv.poll_id) WHERE p.start <= ? GROUP BY p.id ORDER BY p.start DESC, p.end DESC, p.id DESC', array(Core\Registry::get('Date')->getCurrentDateTime()));
		$c_polls = count($polls);

		if ($c_polls > 0) {
			for ($i = 0; $i < $c_polls; ++$i) {
				// Überprüfen, ob der eingeloggte User schon abgestimmt hat
				if (Core\Registry::get('Auth')->isUser() === true) {
					$query = Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'poll_votes WHERE poll_id = ? AND user_id = ?', array($polls[$i]['id'], Core\Registry::get('Auth')->getUserId()));
					// Überprüfung für Gäste
				} else {
					$query = Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'poll_votes WHERE poll_id = ? AND ip = ?', array($polls[$i]['id'], $_SERVER['REMOTE_ADDR']));
				}

				if ($query != 0 ||
						$polls[$i]['start'] != $polls[$i]['end'] && Core\Registry::get('Date')->timestamp($polls[$i]['end']) <= Core\Registry::get('Date')->timestamp()) {
					$polls[$i]['link'] = 'result';
				} else {
					$polls[$i]['link'] = 'vote';
				}
				$polls[$i]['date'] = $polls[$i]['start'] == $polls[$i]['end'] ? '-' : Core\Registry::get('Date')->format($polls[$i]['end']);
			}
			Core\Registry::get('View')->assign('polls', $polls);
		}
	}

	public function actionResult() {
		if (Core\Validate::isNumber(Core\Registry::get('URI')->id) === true &&
				Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'polls WHERE id = ? AND start <= ?', array(Core\Registry::get('URI')->id, Core\Registry::get('Date')->getCurrentDateTime())) == 1) {
			Core\Functions::getRedirectMessage();

			Core\Registry::get('Breadcrumb')
					->append(Core\Registry::get('Lang')->t('polls', 'polls'), Core\Registry::get('URI')->route('polls'))
					->append(Core\Registry::get('Lang')->t('polls', 'result'));

			$question = Core\Registry::get('Db')->fetchAssoc('SELECT p.title, COUNT(pv.poll_id) AS total_votes FROM ' . DB_PRE . 'polls AS p LEFT JOIN ' . DB_PRE . 'poll_votes AS pv ON(p.id = pv.poll_id) WHERE p.id = ?', array(Core\Registry::get('URI')->id));
			$answers = Core\Registry::get('Db')->fetchAll('SELECT pa.id, pa.text, COUNT(pv.answer_id) AS votes FROM ' . DB_PRE . 'poll_answers AS pa LEFT JOIN ' . DB_PRE . 'poll_votes AS pv ON(pa.id = pv.answer_id) WHERE pa.poll_id = ? GROUP BY pa.id ORDER BY pa.id ASC', array(Core\Registry::get('URI')->id));
			$c_answers = count($answers);
			$total_votes = $question['total_votes'];

			for ($i = 0; $i < $c_answers; ++$i) {
				$answers[$i]['percent'] = $total_votes > '0' ? round(100 * $answers[$i]['votes'] / $total_votes, 2) : '0';
			}
			Core\Registry::get('View')->assign('question', $question['title']);
			Core\Registry::get('View')->assign('answers', $answers);
			Core\Registry::get('View')->assign('total_votes', $total_votes);
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

	public function actionSidebar() {
		$period = 'p.start = p.end AND p.start <= :time OR p.start != p.end AND :time BETWEEN p.start AND p.end';
		$poll = Core\Registry::get('Db')->fetchAssoc('SELECT p.id, p.title, p.multiple, COUNT(pv.poll_id) AS total_votes FROM ' . DB_PRE . 'polls AS p LEFT JOIN ' . DB_PRE . 'poll_votes AS pv ON(p.id = pv.poll_id) WHERE ' . $period . ' GROUP BY p.id ORDER BY p.start DESC', array('time' => Core\Registry::get('Date')->getCurrentDateTime()));

		if (!empty($poll)) {
			$answers = Core\Registry::get('Db')->fetchAll('SELECT pa.id, pa.text, COUNT(pv.answer_id) AS votes FROM ' . DB_PRE . 'poll_answers AS pa LEFT JOIN ' . DB_PRE . 'poll_votes AS pv ON(pa.id = pv.answer_id) WHERE pa.poll_id = ? GROUP BY pa.id ORDER BY pa.id ASC', array($poll['id']));
			$c_answers = count($answers);

			Core\Registry::get('View')->assign('sidebar_polls', $poll);

			// Überprüfen, ob der eingeloggte User schon abgestimmt hat
			if (Core\Registry::get('Auth')->isUser() === true) {
				$alreadyVoted = Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'poll_votes WHERE poll_id = ? AND user_id = ?', array($poll['id'], Core\Registry::get('Auth')->getUserId()));
				// Überprüfung für Gäste
			} else {
				$alreadyVoted = Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'poll_votes WHERE poll_id = ? AND ip = ?', array($poll['id'], $_SERVER['REMOTE_ADDR']));
			}

			if ($alreadyVoted > 0) {
				$total_votes = $poll['total_votes'];

				for ($i = 0; $i < $c_answers; ++$i) {
					$votes = $answers[$i]['votes'];
					$answers[$i]['votes'] = ($votes > 1) ? sprintf(Core\Registry::get('Lang')->t('polls', 'number_of_votes'), $votes) : Core\Registry::get('Lang')->t('polls', ($votes == 1 ? 'one_vote' : 'no_votes'));
					$answers[$i]['percent'] = $total_votes > 0 ? round(100 * $votes / $total_votes, 2) : '0';
				}

				Core\Registry::get('View')->assign('sidebar_poll_answers', $answers);
				Core\Registry::get('View')->displayTemplate('polls/sidebar_result.tpl');
			} else {
				Core\Registry::get('View')->assign('sidebar_poll_answers', $answers);
				Core\Registry::get('View')->displayTemplate('polls/sidebar_vote.tpl');
			}
		} else {
			Core\Registry::get('View')->displayTemplate('polls/sidebar_vote.tpl');
		}
	}

	public function actionVote() {
		$time = Core\Registry::get('Date')->getCurrentDateTime();
		$period = ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)';
		$multiple = !empty($_POST['answer']) && is_array($_POST['answer']) ? ' AND multiple = \'1\'' : '';

		if (Core\Validate::isNumber(Core\Registry::get('URI')->id) === true &&
				Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'polls WHERE id = :id' . $period . $multiple, array('id' => Core\Registry::get('URI')->id, 'time' => $time)) == 1) {
			// Brotkrümelspur
			Core\Registry::get('Breadcrumb')
					->append(Core\Registry::get('Lang')->t('polls', 'polls'), Core\Registry::get('URI')->route('polls'))
					->append(Core\Registry::get('Lang')->t('polls', 'vote'));

			// Wenn abgestimmt wurde
			if (!empty($_POST['answer']) && (is_array($_POST['answer']) === true || Core\Validate::isNumber($_POST['answer']) === true)) {
				$ip = $_SERVER['REMOTE_ADDR'];
				$answers = $_POST['answer'];

				// Überprüfen, ob der eingeloggte User schon abgestimmt hat
				if (Core\Registry::get('Auth')->isUser() === true) {
					$query = Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'poll_votes WHERE poll_id = ? AND user_id = ?', array(Core\Registry::get('URI')->id, Core\Registry::get('Auth')->getUserId()));
					// Überprüfung für Gäste
				} else {
					$query = Core\Registry::get('Db')->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'poll_votes WHERE poll_id = ? AND ip = ?', array(Core\Registry::get('URI')->id, $ip));
				}

				if ($query == 0) {
					$user_id = Core\Registry::get('Auth')->isUser() ? Core\Registry::get('Auth')->getUserId() : 0;

					if (is_array($answers) === true) {
						foreach ($answers as $answer) {
							if (Core\Validate::isNumber($answer) === true) {
								$insert_values = array(
									'poll_id' => Core\Registry::get('URI')->id,
									'answer_id' => $answer,
									'user_id' => $user_id,
									'ip' => $ip,
									'time' => $time,
								);
								Core\Registry::get('Db')->insert(DB_PRE . 'poll_votes', $insert_values);
							}
						}
						$bool = true;
					} else {
						$insert_values = array(
							'poll_id' => Core\Registry::get('URI')->id,
							'answer_id' => $answers,
							'user_id' => $user_id,
							'ip' => $ip,
							'time' => $time,
						);
						$bool = Core\Registry::get('Db')->insert(DB_PRE . 'poll_votes', $insert_values);
					}
					$text = $bool !== false ? Core\Registry::get('Lang')->t('polls', 'poll_success') : Core\Registry::get('Lang')->t('polls', 'poll_error');
				} else {
					$text = Core\Registry::get('Lang')->t('polls', 'already_voted');
				}
				Core\Functions::setRedirectMessage($bool, $text, 'polls/result/id_' . Core\Registry::get('URI')->id);
			} else {
				$poll = Core\Registry::get('Db')->fetchAssoc('SELECT title, multiple FROM ' . DB_PRE . 'polls WHERE id = ?', array(Core\Registry::get('URI')->id));
				$answers = Core\Registry::get('Db')->fetchAll('SELECT id, text FROM ' . DB_PRE . 'poll_answers WHERE poll_id = ? ORDER BY id ASC', array(Core\Registry::get('URI')->id));

				Core\Registry::get('View')->assign('question', $poll['title']);
				Core\Registry::get('View')->assign('multiple', $poll['multiple']);
				Core\Registry::get('View')->assign('answers', $answers);
			}
		} else {
			Core\Registry::get('URI')->redirect('errors/404');
		}
	}

}