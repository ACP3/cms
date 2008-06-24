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

$period = ' AND (start = end AND start <= \'' . $date->timestamp() . '\' OR start != end AND start <= \'' . $date->timestamp() . '\' AND end >= \'' . $date->timestamp() . '\')';

if (validate::isNumber($uri->id) && $db->select('id', 'poll_question', 'id = \'' . $uri->id . '\'' . $period, 0, 0, 0, 1) == 1) {
	// Brotkrümelspur
	breadcrumb::assign($lang->t('polls', 'polls'), uri('polls'));
	breadcrumb::assign($lang->t('polls', 'vote'));

	if (isset($_POST['submit']) && isset($_POST['answer']) && validate::isNumber($_POST['answer'])) {
		$answer = $_POST['answer'];
		$ip = $_SERVER['REMOTE_ADDR'];
		$time = $date->timestamp();

		if ($db->select('poll_id', 'poll_votes', 'poll_id = \'' . $uri->id . '\' AND ip = \'' . $ip . '\'', 0, 0, 0, 1) == 0) {
			$insert_values = array(
				'poll_id' => $uri->id,
				'answer_id' => $answer,
				'ip' => $ip,
				'time' => $time,
			);
			$bool = $db->insert('poll_votes', $insert_values);

			$text = $bool ? $lang->t('polls', 'poll_success') : $lang->t('polls', 'poll_error');
		} else {
			$text = $lang->t('polls', 'already_voted');
		}
		$content = comboBox($text, uri('polls/result/id_' . $uri->id));
	} else {
		$question = $db->select('question', 'poll_question', 'id = \'' . $uri->id . '\'');
		$answers = $db->select('id, text', 'poll_answers', 'poll_id = \'' . $uri->id . '\'', 'id ASC');
		$c_answers = count($answers);

		$css_class = 'dark';
		for ($i = 0; $i < $c_answers; ++$i) {
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