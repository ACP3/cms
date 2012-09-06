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

$period = 'p.start = p.end AND p.start <= :time OR p.start != p.end AND :time BETWEEN p.start AND p.end';
$question = ACP3_CMS::$db2->fetchAssoc('SELECT p.id, p.question, p.multiple, COUNT(pv.poll_id) AS total_votes FROM ' . DB_PRE . 'polls AS p LEFT JOIN ' . DB_PRE . 'poll_votes AS pv ON(p.id = pv.poll_id) WHERE ' . $period . ' GROUP BY p.id ORDER BY p.start DESC', array('time' => ACP3_CMS::$date->getCurrentDateTime()));

if (!empty($question)) {
	$answers = ACP3_CMS::$db2->fetchAll('SELECT pa.id, pa.text, COUNT(pv.answer_id) AS votes FROM ' . DB_PRE . 'poll_answers AS pa LEFT JOIN ' . DB_PRE . 'poll_votes AS pv ON(pa.id = pv.answer_id) WHERE pa.poll_id = ? GROUP BY pa.id ORDER BY pa.id ASC', array($question['id']));
	$c_answers = count($answers);

	ACP3_CMS::$view->assign('sidebar_polls', $question);

	// Überprüfen, ob der eingeloggte User schon abgestimmt hat
	if (ACP3_CMS::$auth->isUser() === true) {
		$alreadyVoted = ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'poll_votes WHERE poll_id = ? AND user_id = ?', array($question['id'], ACP3_CMS::$auth->getUserId()));
	// Überprüfung für Gäste
	} else {
		$alreadyVoted = ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'poll_votes WHERE poll_id = ? AND ip = ?', array($question['id'], $_SERVER['REMOTE_ADDR']));
	}

	if ($alreadyVoted > 0) {
		$total_votes = $question['total_votes'];

		for ($i = 0; $i < $c_answers; ++$i) {
			$votes = $answers[$i]['votes'];
			$answers[$i]['votes'] = ($votes > 1) ? sprintf(ACP3_CMS::$lang->t('polls', 'number_of_votes'), $votes) : ACP3_CMS::$lang->t('polls', ($votes == 1 ? 'one_vote' : 'no_votes'));
			$answers[$i]['percent'] = $total_votes > 0 ? round(100 * $votes / $total_votes, 2) : '0';
		}

		ACP3_CMS::$view->assign('sidebar_poll_answers', $answers);
		ACP3_CMS::$view->displayTemplate('polls/sidebar_result.tpl');
	} else {
		ACP3_CMS::$view->assign('sidebar_poll_answers', $answers);
		ACP3_CMS::$view->displayTemplate('polls/sidebar_vote.tpl');
	}
} else {
	ACP3_CMS::$view->displayTemplate('polls/sidebar_vote.tpl');
}