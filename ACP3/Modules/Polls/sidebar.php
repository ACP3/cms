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
$poll = ACP3\CMS::$injector['Db']->fetchAssoc('SELECT p.id, p.title, p.multiple, COUNT(pv.poll_id) AS total_votes FROM ' . DB_PRE . 'polls AS p LEFT JOIN ' . DB_PRE . 'poll_votes AS pv ON(p.id = pv.poll_id) WHERE ' . $period . ' GROUP BY p.id ORDER BY p.start DESC', array('time' => ACP3\CMS::$injector['Date']->getCurrentDateTime()));

if (!empty($poll)) {
	$answers = ACP3\CMS::$injector['Db']->fetchAll('SELECT pa.id, pa.text, COUNT(pv.answer_id) AS votes FROM ' . DB_PRE . 'poll_answers AS pa LEFT JOIN ' . DB_PRE . 'poll_votes AS pv ON(pa.id = pv.answer_id) WHERE pa.poll_id = ? GROUP BY pa.id ORDER BY pa.id ASC', array($poll['id']));
	$c_answers = count($answers);

	ACP3\CMS::$injector['View']->assign('sidebar_polls', $poll);

	// Überprüfen, ob der eingeloggte User schon abgestimmt hat
	if (ACP3\CMS::$injector['Auth']->isUser() === true) {
		$alreadyVoted = ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'poll_votes WHERE poll_id = ? AND user_id = ?', array($poll['id'], ACP3\CMS::$injector['Auth']->getUserId()));
	// Überprüfung für Gäste
	} else {
		$alreadyVoted = ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'poll_votes WHERE poll_id = ? AND ip = ?', array($poll['id'], $_SERVER['REMOTE_ADDR']));
	}

	if ($alreadyVoted > 0) {
		$total_votes = $poll['total_votes'];

		for ($i = 0; $i < $c_answers; ++$i) {
			$votes = $answers[$i]['votes'];
			$answers[$i]['votes'] = ($votes > 1) ? sprintf(ACP3\CMS::$injector['Lang']->t('polls', 'number_of_votes'), $votes) : ACP3\CMS::$injector['Lang']->t('polls', ($votes == 1 ? 'one_vote' : 'no_votes'));
			$answers[$i]['percent'] = $total_votes > 0 ? round(100 * $votes / $total_votes, 2) : '0';
		}

		ACP3\CMS::$injector['View']->assign('sidebar_poll_answers', $answers);
		ACP3\CMS::$injector['View']->displayTemplate('polls/sidebar_result.tpl');
	} else {
		ACP3\CMS::$injector['View']->assign('sidebar_poll_answers', $answers);
		ACP3\CMS::$injector['View']->displayTemplate('polls/sidebar_vote.tpl');
	}
} else {
	ACP3\CMS::$injector['View']->displayTemplate('polls/sidebar_vote.tpl');
}