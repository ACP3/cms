<?php
/**
 * Polls
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

$time = ACP3\CMS::$injector['Date']->getCurrentDateTime();
$period = ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)';
$multiple = !empty($_POST['answer']) && is_array($_POST['answer']) ? ' AND multiple = \'1\'' : '';

if (ACP3\Core\Validate::isNumber(ACP3\CMS::$injector['URI']->id) === true &&
	ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'polls WHERE id = :id' . $period . $multiple, array('id' => ACP3\CMS::$injector['URI']->id, 'time' => $time)) == 1) {
	// Brotkrümelspur
	ACP3\CMS::$injector['Breadcrumb']
	->append(ACP3\CMS::$injector['Lang']->t('polls', 'polls'), ACP3\CMS::$injector['URI']->route('polls'))
	->append(ACP3\CMS::$injector['Lang']->t('polls', 'vote'));

	// Wenn abgestimmt wurde
	if (!empty($_POST['answer']) && (is_array($_POST['answer']) === true || ACP3\Core\Validate::isNumber($_POST['answer']) === true)) {
		$ip = $_SERVER['REMOTE_ADDR'];
		$answers = $_POST['answer'];

		// Überprüfen, ob der eingeloggte User schon abgestimmt hat
		if (ACP3\CMS::$injector['Auth']->isUser() === true) {
			$query = ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'poll_votes WHERE poll_id = ? AND user_id = ?', array(ACP3\CMS::$injector['URI']->id ,ACP3\CMS::$injector['Auth']->getUserId()));
		// Überprüfung für Gäste
		} else {
			$query = ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'poll_votes WHERE poll_id = ? AND ip = ?', array(ACP3\CMS::$injector['URI']->id, $ip));
		}

		if ($query == 0) {
			$user_id = ACP3\CMS::$injector['Auth']->isUser() ? ACP3\CMS::$injector['Auth']->getUserId() : 0;

			if (is_array($answers) === true) {
				foreach ($answers as $answer) {
					if (ACP3\Core\Validate::isNumber($answer) === true) {
						$insert_values = array(
							'poll_id' => ACP3\CMS::$injector['URI']->id,
							'answer_id' => $answer,
							'user_id' => $user_id,
							'ip' => $ip,
							'time' => $time,
						);
						ACP3\CMS::$injector['Db']->insert(DB_PRE . 'poll_votes', $insert_values);
					}
				}
				$bool = true;
			} else {
				$insert_values = array(
					'poll_id' => ACP3\CMS::$injector['URI']->id,
					'answer_id' => $answers,
					'user_id' => $user_id,
					'ip' => $ip,
					'time' => $time,
				);
				$bool = ACP3\CMS::$injector['Db']->insert(DB_PRE . 'poll_votes', $insert_values);
			}
			$text = $bool !== false ? ACP3\CMS::$injector['Lang']->t('polls', 'poll_success') : ACP3\CMS::$injector['Lang']->t('polls', 'poll_error');
		} else {
			$text = ACP3\CMS::$injector['Lang']->t('polls', 'already_voted');
		}
		ACP3\Core\Functions::setRedirectMessage($bool, $text, 'polls/result/id_' . ACP3\CMS::$injector['URI']->id);
	} else {
		$poll = ACP3\CMS::$injector['Db']->fetchAssoc('SELECT title, multiple FROM ' . DB_PRE . 'polls WHERE id = ?', array(ACP3\CMS::$injector['URI']->id));
		$answers = ACP3\CMS::$injector['Db']->fetchAll('SELECT id, text FROM ' . DB_PRE . 'poll_answers WHERE poll_id = ? ORDER BY id ASC', array(ACP3\CMS::$injector['URI']->id));

		ACP3\CMS::$injector['View']->assign('question', $poll['title']);
		ACP3\CMS::$injector['View']->assign('multiple', $poll['multiple']);
		ACP3\CMS::$injector['View']->assign('answers', $answers);
	}
} else {
	ACP3\CMS::$injector['URI']->redirect('errors/404');
}