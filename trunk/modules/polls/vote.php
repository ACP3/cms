<?php
/**
 * Polls
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

$time = $date->timestamp();
$period = ' AND (start = end AND start <= ' . $time . ' OR start != end AND start <= ' . $time . ' AND end >= ' . $time . ')';
$multiple = !empty($_POST['answer']) && is_array($_POST['answer']) ? ' AND multiple = \'1\'' : '';

if (ACP3_Validate::isNumber($uri->id) === true && $db->countRows('*', 'polls', 'id = \'' . $uri->id . '\'' . $multiple . $period) == 1) {
	// Brotkrümelspur
	$breadcrumb->append($lang->t('polls', 'polls'), $uri->route('polls'))
			   ->append($lang->t('polls', 'vote'));

	// Wenn abgestimmt wurde
	if (!empty($_POST['answer']) && (is_array($_POST['answer']) === true || ACP3_Validate::isNumber($_POST['answer']) === true)) {
		$ip = $_SERVER['REMOTE_ADDR'];
		$answers = $_POST['answer'];

		// Überprüfen, ob der eingeloggte User schon abgestimmt hat
		if ($auth->isUser() === true) {
			$query = $db->countRows('poll_id', 'poll_votes', 'poll_id = \'' . $uri->id . '\' AND user_id = \'' . $auth->getUserId() . '\'');
		// Überprüfung für Gäste
		} else {
			$query = $db->countRows('poll_id', 'poll_votes', 'poll_id = \'' . $uri->id . '\' AND ip = \'' . $ip . '\'');
		}

		if ($query == 0) {
			$user_id = $auth->isUser() ? $auth->getUserId() : 0;

			if (is_array($answers) === true) {
				foreach ($answers as $answer) {
					if (ACP3_Validate::isNumber($answer) === true) {
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
			$text = $bool !== false ? $lang->t('polls', 'poll_success') : $lang->t('polls', 'poll_error');
		} else {
			$text = $lang->t('polls', 'already_voted');
		}
		ACP3_View::setContent(confirmBox($text, $uri->route('polls/result/id_' . $uri->id)));
	} else {
		$question = $db->select('question, multiple', 'polls', 'id = \'' . $uri->id . '\'');
		$answers = $db->select('id, text', 'poll_answers', 'poll_id = \'' . $uri->id . '\'', 'id ASC');
		$c_answers = count($answers);

		$css_class = 'dark';
		for ($i = 0; $i < $c_answers; ++$i) {
			$css_class = $css_class == 'dark' ? 'light' : 'dark';
			$answers[$i]['css_class'] = $css_class;
			$answers[$i]['text'] = $db->escape($answers[$i]['text'], 3);
		}

		$tpl->assign('question', $db->escape($question[0]['question'], 3));
		$tpl->assign('multiple', $question[0]['multiple']);
		$tpl->assign('answers', $answers);

		ACP3_View::setContent(ACP3_View::fetchTemplate('polls/vote.tpl'));
	}
} else {
	$uri->redirect('errors/404');
}