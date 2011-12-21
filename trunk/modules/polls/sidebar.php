<?php
/**
 * Polls
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3') && !defined('IN_ADM'))
	exit;

$time = $date->timestamp();
$period = '(start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\')';

$question = $db->select('id, question, multiple', 'polls', $period, 'start DESC');

if (count($question) > 0) {
	$answers = $db->select('id, text', 'poll_answers', 'poll_id = \'' . $question[0]['id'] . '\'', 'id ASC');
	$c_answers = count($answers);

	$tpl->assign('sidebar_polls', $question[0]);

	// Überprüfen, ob der eingeloggte User schon abgestimmt hat
	if ($auth->isUser())
		$alreadyVoted = $db->countRows('poll_id', 'poll_votes', 'poll_id = \'' . $question[0]['id'] . '\' AND user_id = \'' . $auth->getUserId() . '\'');
	// Überprüfung für Gäste
	else
		$alreadyVoted = $db->countRows('poll_id', 'poll_votes', 'poll_id = \'' . $question[0]['id'] . '\' AND ip = \'' . $_SERVER['REMOTE_ADDR'] . '\'');

	if ($alreadyVoted > 0) {
		$total_votes = $db->countRows('answer_id', 'poll_votes', 'poll_id = \'' . $question[0]['id'] . '\'');

		for ($i = 0; $i < $c_answers; ++$i) {
			$votes = $db->countRows('answer_id', 'poll_votes', 'answer_id = \'' . $answers[$i]['id'] . '\'');
			$answers[$i]['votes'] = ($votes > 1) ? sprintf($lang->t('polls', 'number_of_votes'), $votes) : (($votes == 1) ? $lang->t('polls', 'one_vote') : $lang->t('polls', 'no_votes'));
			$answers[$i]['percent'] = $total_votes > 0 ? round(100 * $votes / $total_votes, 2) : '0';
		}

		$tpl->assign('sidebar_poll_answers', $answers);
		modules::displayTemplate('polls/sidebar_result.html');
	} else {
		$tpl->assign('sidebar_poll_answers', $answers);
		modules::displayTemplate('polls/sidebar_vote.html');
	}
} else {
	modules::displayTemplate('polls/sidebar_vote.html');
}