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

if (ACP3_Validate::isNumber($uri->id) === true && $db->countRows('*', 'polls', 'id = \'' . $uri->id . '\' AND start <= \'' . $date->getCurrentDateTime() . '\'') == 1) {
	$breadcrumb->append($lang->t('polls', 'polls'), $uri->route('polls'))
			   ->append($lang->t('polls', 'result'));

	$question = $db->select('question', 'polls');
	$answers = $db->select('id, text', 'poll_answers', 'poll_id = \'' . $uri->id . '\'', 'id ASC');
	$c_answers = count($answers);
	$total_votes = $db->countRows('answer_id', 'poll_votes', 'poll_id = \'' . $uri->id . '\'');

	for ($i = 0; $i < $c_answers; ++$i) {
		$answers[$i]['text'] = $db->escape($answers[$i]['text'], 3);
		$answers[$i]['votes'] = $db->countRows('answer_id', 'poll_votes', 'answer_id = \'' . $answers[$i]['id'] . '\'');
		$answers[$i]['percent'] = $total_votes > '0' ? round(100 * $answers[$i]['votes'] / $total_votes, 2) : '0';
	}
	$tpl->assign('question', $db->escape($question[0]['question'], 3));
	$tpl->assign('answers', $answers);
	$tpl->assign('total_votes', $total_votes);

	ACP3_View::setContent(ACP3_View::fetchTemplate('polls/result.tpl'));
} else {
	$uri->redirect('errors/404');
}
