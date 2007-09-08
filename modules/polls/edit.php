<?php
/**
 * Polls
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (!empty($modules->id) && $db->select('id', 'poll_question', 'id = \'' . $modules->id . '\'', 0, 0, 0, 1) == '1') {
	if (isset($_POST['submit'])) {
		include 'modules/polls/entry.php';
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$poll = $db->select('start, end, question', 'poll_question', 'id = \'' . $modules->id . '\'');

		// Datum
		$start_date = explode('.', date_aligned(1, $poll[0]['start'], 'j.n.Y.G.i'));
		$end_date = explode('.', date_aligned(1, $poll[0]['end'], 'j.n.Y.G.i'));

		// Datumsauswahl
		$tpl->assign('start_day', date_dropdown('day', 'start_day', 'start_day', $start_date[0]));
		$tpl->assign('start_month', date_dropdown('month', 'start_month', 'start_month', $start_date[1]));
		$tpl->assign('start_year', date_dropdown('year', 'start_year', 'start_year', $start_date[2]));
		$tpl->assign('start_hour', date_dropdown('hour', 'start_hour', 'start_hour', $start_date[3]));
		$tpl->assign('start_min', date_dropdown('min', 'start_min', 'start_min', $start_date[4]));
		$tpl->assign('end_day', date_dropdown('day', 'end_day', 'end_day', $end_date[0]));
		$tpl->assign('end_month', date_dropdown('month', 'end_month', 'end_month', $end_date[1]));
		$tpl->assign('end_year', date_dropdown('year', 'end_year', 'end_year', $end_date[2]));
		$tpl->assign('end_hour', date_dropdown('hour', 'end_hour', 'end_hour', $end_date[3]));
		$tpl->assign('end_min', date_dropdown('min', 'end_min', 'end_min', $end_date[4]));

		$tpl->assign('question', isset($form['question']) ? $form['question'] : $poll[0]['question']);

		$answers = $db->select('id, text', 'poll_answers', 'poll_id = \'' . $modules->id . '\'');
		$c_answers = count($answers);

		for ($i = 0; $i < $c_answers; $i++) {
			$answers[$i]['number'] = $i + 1;
			$answers[$i]['id'] = $answers[$i]['id'];
			$answers[$i]['value'] = $answers[$i]['text'];
		}
		$tpl->assign('answers', $answers);

		$content = $tpl->fetch('polls/edit.html');
	}
} else {
	redirect('errors/404');
}
?>