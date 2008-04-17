<?php
/**
 * Polls
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;

if (validate::isNumber($modules->id) && $db->select('id', 'poll_question', 'id = \'' . $modules->id . '\' AND start <= \'' . dateAligned(2, time()) . '\'', 0, 0, 0, 1) == 1) {
	$breadcrumb->assign(lang('polls', 'polls'), uri('polls'));
	$breadcrumb->assign(lang('polls', 'result'));

	$question = $db->select('question', 'poll_question');
	$answers = $db->select('id, text', 'poll_answers', 'poll_id = \'' . $modules->id . '\'', 'id ASC');
	$c_answers = count($answers);
	$total_votes = $db->select('answer_id', 'poll_votes', 'poll_id = \'' . $modules->id . '\'', 0, 0, 0, 1);

	for ($i = 0; $i < $c_answers; ++$i) {
		$answers[$i]['text'] = $answers[$i]['text'];
		$answers[$i]['votes'] = $db->select('answer_id', 'poll_votes', 'poll_id = \'' . $modules->id . '\' AND answer_id = \'' . $answers[$i]['id'] . '\'', 0, 0, 0, 1);
		$answers[$i]['percent'] = $total_votes > '0' ? 100 * $answers[$i]['votes'] / $total_votes : '0';
	}
	$tpl->assign('question', $question[0]['question']);
	$tpl->assign('answers', $answers);
	$tpl->assign('total_votes', $total_votes);

	$content = $tpl->fetch('polls/result.html');
} else {
	redirect('errors/404');
}
?>