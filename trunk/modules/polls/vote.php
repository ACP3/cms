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

$time = ACP3_CMS::$date->getCurrentDateTime();
$period = ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)';
$multiple = !empty($_POST['answer']) && is_array($_POST['answer']) ? ' AND multiple = \'1\'' : '';

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true &&
	ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'polls WHERE id = :id' . $period . $multiple, array('id' => ACP3_CMS::$uri->id, 'time' => $time)) == 1) {
	// Brotkrümelspur
	ACP3_CMS::$breadcrumb
	->append(ACP3_CMS::$lang->t('polls', 'polls'), ACP3_CMS::$uri->route('polls'))
	->append(ACP3_CMS::$lang->t('polls', 'vote'));

	// Wenn abgestimmt wurde
	if (!empty($_POST['answer']) && (is_array($_POST['answer']) === true || ACP3_Validate::isNumber($_POST['answer']) === true)) {
		$ip = $_SERVER['REMOTE_ADDR'];
		$answers = $_POST['answer'];

		// Überprüfen, ob der eingeloggte User schon abgestimmt hat
		if (ACP3_CMS::$auth->isUser() === true) {
			$query = ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'poll_votes WHERE poll_id = ? AND user_id = ?', array(ACP3_CMS::$uri->id ,ACP3_CMS::$auth->getUserId()));
		// Überprüfung für Gäste
		} else {
			$query = ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'poll_votes WHERE poll_id = ? AND ip = ?', array(ACP3_CMS::$uri->id, $ip));
		}

		if ($query == 0) {
			$user_id = ACP3_CMS::$auth->isUser() ? ACP3_CMS::$auth->getUserId() : 0;

			if (is_array($answers) === true) {
				foreach ($answers as $answer) {
					if (ACP3_Validate::isNumber($answer) === true) {
						$insert_values = array(
							'poll_id' => ACP3_CMS::$uri->id,
							'answer_id' => $answer,
							'user_id' => $user_id,
							'ip' => $ip,
							'time' => $time,
						);
						ACP3_CMS::$db2->insert(DB_PRE . 'poll_votes', $insert_values);
					}
				}
				$bool = true;
			} else {
				$insert_values = array(
					'poll_id' => ACP3_CMS::$uri->id,
					'answer_id' => $answers,
					'user_id' => $user_id,
					'ip' => $ip,
					'time' => $time,
				);
				$bool = ACP3_CMS::$db2->insert(DB_PRE . 'poll_votes', $insert_values);
			}
			$text = $bool !== false ? ACP3_CMS::$lang->t('polls', 'poll_success') : ACP3_CMS::$lang->t('polls', 'poll_error');
		} else {
			$text = ACP3_CMS::$lang->t('polls', 'already_voted');
		}
		setRedirectMessage($bool, $text, 'polls/result/id_' . ACP3_CMS::$uri->id);
	} else {
		$question = ACP3_CMS::$db2->fetchAssoc('SELECT question, multiple FROM ' . DB_PRE . 'polls WHERE id = ?', array(ACP3_CMS::$uri->id));
		$answers = ACP3_CMS::$db2->fetchAll('SELECT id, text FROM ' . DB_PRE . 'poll_answers WHERE poll_id = ? ORDER BY id ASC', array(ACP3_CMS::$uri->id));

		ACP3_CMS::$view->assign('question', $question['question']);
		ACP3_CMS::$view->assign('multiple', $question['multiple']);
		ACP3_CMS::$view->assign('answers', $answers);

		ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('polls/vote.tpl'));
	}
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}