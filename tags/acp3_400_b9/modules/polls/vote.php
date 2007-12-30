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

$date = ' AND (start = end AND start <= \'' . date_aligned(2, time()) . '\' OR start != end AND start <= \'' . date_aligned(2, time()) . '\' AND end >= \'' . date_aligned(2, time()) . '\')';

if (!empty($modules->id) && $db->select('id', 'poll_question', 'id = \'' . $modules->id . '\'' . $date, 0, 0, 0, 1) == 1) {
	// Brotkrümelspur
	$breadcrumb->assign(lang('polls', 'polls'), uri('polls'));
	$breadcrumb->assign(lang('polls', 'vote'));

	if (isset($_POST['submit']) && isset($_POST['answer']) && $validate->is_number($_POST['answer'])) {
		$answer = $_POST['answer'];
		$ip = $_SERVER['REMOTE_ADDR'];
		$time = date_aligned(2, time());

		if ($db->select('poll_id', 'poll_votes', 'poll_id = \'' . $modules->id . '\' AND ip = \'' . $ip . '\'', 0, 0, 0, 1) == 0) {
			$insert_values = array(
				'poll_id' => $modules->id,
				'answer_id' => $answer,
				'ip' => $ip,
				'time' => $time,
			);
			$bool = $db->insert('poll_votes', $insert_values);

			$text = $bool ? lang('polls', 'poll_success') : lang('polls', 'poll_error');
		} else {
			$text = lang('polls', 'already_voted');
		}
		$content = combo_box($text, uri('polls/result/id_' . $modules->id));
	} else {
		$question = $db->select('question', 'poll_question', 'id = \'' . $modules->id . '\'');
		$answers = $db->select('id, text', 'poll_answers', 'poll_id = \'' . $modules->id . '\'', 'id ASC');
		$c_answers = count($answers);

		$css_class = 'dark';
		for ($i = 0; $i < $c_answers; $i++) {
			$css_class = $css_class == 'dark' ? 'light' : 'dark';
			$answers[$i]['css_class'] = $css_class;
		}

		$tpl->assign('question', $question[0]['question']);
		$tpl->assign('answers', $answers);

		$content = $tpl->fetch('polls/vote.html');
	}
} else {
	redirect('errors/404');
}
?>