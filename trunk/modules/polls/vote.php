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
$multiple = !empty($_POST['answer']) && is_array($_POST['answer']) ? ' AND multiple = \'1\'' : '';

if (validate::isNumber($uri->id) && $db->countRows('*', 'poll_question', 'id = \'' . $uri->id . '\'' . $multiple . $period) == 1) {
	// Brotkrümelspur
	breadcrumb::assign($lang->t('polls', 'polls'), uri('polls'));
	breadcrumb::assign($lang->t('polls', 'vote'));

	// Wenn abgestimmt wurde
	if (!empty($_POST['answer']) && (is_array($_POST['answer']) || validate::isNumber($_POST['answer']))) {
		$ip = $_SERVER['REMOTE_ADDR'];
		$answers = $_POST['answer'];

		// Überprüfen, ob der eingeloggte User schon abgestimmt hat
		if ($auth->isUser()) {
			$query = $db->countRows('poll_id', 'poll_votes', 'poll_id = \'' . $uri->id . '\' AND user_id = \'' . $auth->getUserId() . '\'');
		// Überprüfung für Gäste
		} else {
			$query = $db->countRows('poll_id', 'poll_votes', 'poll_id = \'' . $uri->id . '\' AND ip = \'' . $ip . '\'');
		}

		if ($query == 0) {
			$user_id = $auth->isUser() ? $auth->getUserId() : 0;

			if (is_array($answers)) {
				foreach ($answers as $answer) {
					if (validate::isNumber($answer)) {
						$insert_values = array(
							'poll_id' => $uri->id,
							'answer_id' => $answer,
							'user_id' => $user_id,
							'ip' => $ip,
							'time' => $time,
						);
						$db->insert('poll_votes', $insert_values);
					}
				}
				$bool = true;
			} else {
				$insert_values = array(
					'poll_id' => $uri->id,
					'answer_id' => $answers,
					'user_id' => $user_id,
					'ip' => $ip,
					'time' => $time,
				);
				$bool = $db->insert('poll_votes', $insert_values);
			}
			$text = $bool !== null ? $lang->t('polls', 'poll_success') : $lang->t('polls', 'poll_error');
		} else {
			$text = $lang->t('polls', 'already_voted');
		}
		$content = comboBox($text, uri('polls/result/id_' . $uri->id));
	} else {
		$question = $db->select('question, multiple', 'poll_question', 'id = \'' . $uri->id . '\'');
		$answers = $db->select('id, text', 'poll_answers', 'poll_id = \'' . $uri->id . '\'', 'id ASC');
		$c_answers = count($answers);

		$css_class = 'dark';
		for ($i = 0; $i < $c_answers; ++$i) {
			$css_class = $css_class == 'dark' ? 'light' : 'dark';
			$answers[$i]['css_class'] = $css_class;
		}

		$tpl->assign('question', $question[0]['question']);
		$tpl->assign('multiple', $question[0]['multiple']);
		$tpl->assign('answers', $answers);

		$content = modules::fetchTemplate('polls/vote.html');
	}
} else {
	redirect('errors/404');
}