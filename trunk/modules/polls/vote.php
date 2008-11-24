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

$time = $date->timestamp();
$period = ' AND (start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\')';

if (validate::isNumber($uri->id) && $db->select('id', 'poll_question', 'id = \'' . $uri->id . '\'' . $period, 0, 0, 0, 1) == 1) {
	// Brotkrümelspur
	breadcrumb::assign($lang->t('polls', 'polls'), uri('polls'));
	breadcrumb::assign($lang->t('polls', 'vote'));

	// Wenn abgestimmt wurde
	if (isset($_POST['submit']) && isset($_POST['answer']) && validate::isNumber($_POST['answer'])) {
		$ip = $_SERVER['REMOTE_ADDR'];

		// Überprüfen, ob der eingeloggter User schon abgestimmt hat
		if ($auth->isUser()) {
			$query = $db->select('poll_id', 'poll_votes', 'poll_id = \'' . $uri->id . '\' AND user_id = \'' . USER_ID . '\'', 0, 0, 0, 1);
		// Überprüfung für Gäste
		} else {
			$query = $db->select('poll_id', 'poll_votes', 'poll_id = \'' . $uri->id . '\' AND ip = \'' . $ip . '\'', 0, 0, 0, 1);
		}

		if ($query == 0) {
			$insert_values = array(
				'poll_id' => $uri->id,
				'answer_id' => $_POST['answer'],
				'user_id' => $auth->isUser() ? USER_ID : 0,
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