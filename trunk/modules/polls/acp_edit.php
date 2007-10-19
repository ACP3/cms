<?php
/**
 * Polls
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP'))
	exit;

if (!empty($modules->id) && $db->select('id', 'poll_question', 'id = \'' . $modules->id . '\'', 0, 0, 0, 1) == '1') {
	if (isset($_POST['submit'])) {
		include 'modules/polls/entry.php';
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$poll = $db->select('start, end, question', 'poll_question', 'id = \'' . $modules->id . '\'');

		// Datumsauswahl
		$tpl->assign('start_date', publication_period('start', $poll[0]['start']));
		$tpl->assign('end_date', publication_period('end', $poll[0]['end']));

		$tpl->assign('question', isset($form['question']) ? $form['question'] : $poll[0]['question']);

		$answers = $db->select('id, text', 'poll_answers', 'poll_id = \'' . $modules->id . '\'');
		$c_answers = count($answers);

		for ($i = 0; $i < $c_answers; $i++) {
			$answers[$i]['number'] = $i + 1;
			$answers[$i]['id'] = $answers[$i]['id'];
			$answers[$i]['value'] = $answers[$i]['text'];
		}
		$tpl->assign('answers', $answers);

		$content = $tpl->fetch('polls/acp_edit.html');
	}
} else {
	redirect('errors/404');
}
?>