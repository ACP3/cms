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
$period = '(start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\')';

$question = ACP3_CMS::$db->select('id, question, multiple', 'polls', $period, 'start DESC');

if (count($question) > 0) {
	$answers = ACP3_CMS::$db->select('id, text', 'poll_answers', 'poll_id = \'' . $question[0]['id'] . '\'', 'id ASC');
	$c_answers = count($answers);

	$question[0]['question'] = ACP3_CMS::$db->escape($question[0]['question'], 3);
	ACP3_CMS::$view->assign('sidebar_polls', $question[0]);

	// Überprüfen, ob der eingeloggte User schon abgestimmt hat
	if (ACP3_CMS::$auth->isUser() === true)
		$alreadyVoted = ACP3_CMS::$db->countRows('poll_id', 'poll_votes', 'poll_id = \'' . $question[0]['id'] . '\' AND user_id = \'' . ACP3_CMS::$auth->getUserId() . '\'');
	// Überprüfung für Gäste
	else
		$alreadyVoted = ACP3_CMS::$db->countRows('poll_id', 'poll_votes', 'poll_id = \'' . $question[0]['id'] . '\' AND ip = \'' . $_SERVER['REMOTE_ADDR'] . '\'');

	if ($alreadyVoted > 0) {
		$total_votes = ACP3_CMS::$db->countRows('answer_id', 'poll_votes', 'poll_id = \'' . $question[0]['id'] . '\'');

		for ($i = 0; $i < $c_answers; ++$i) {
			$answers[$i]['text'] = ACP3_CMS::$db->escape($answers[$i]['text'], 3);
			$votes = ACP3_CMS::$db->countRows('answer_id', 'poll_votes', 'answer_id = \'' . $answers[$i]['id'] . '\'');
			$answers[$i]['votes'] = ($votes > 1) ? sprintf(ACP3_CMS::$lang->t('polls', 'number_of_votes'), $votes) : (($votes == 1) ? ACP3_CMS::$lang->t('polls', 'one_vote') : ACP3_CMS::$lang->t('polls', 'no_votes'));
			$answers[$i]['percent'] = $total_votes > 0 ? round(100 * $votes / $total_votes, 2) : '0';
		}

		ACP3_CMS::$view->assign('sidebar_poll_answers', $answers);
		ACP3_CMS::$view->displayTemplate('polls/sidebar_result.tpl');
	} else {
		for ($i = 0; $i < $c_answers; ++$i) {
			$answers[$i]['text'] = ACP3_CMS::$db->escape($answers[$i]['text'], 3);
		}

		ACP3_CMS::$view->assign('sidebar_poll_answers', $answers);
		ACP3_CMS::$view->displayTemplate('polls/sidebar_vote.tpl');
	}
} else {
	ACP3_CMS::$view->displayTemplate('polls/sidebar_vote.tpl');
}