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
$period = ' AND (start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\')';
$multiple = !empty($_POST['answer']) && is_array($_POST['answer']) ? ' AND multiple = \'1\'' : '';

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true && ACP3_CMS::$db->countRows('*', 'polls', 'id = \'' . ACP3_CMS::$uri->id . '\'' . $multiple . $period) == 1) {
	// Brotkrümelspur
	ACP3_CMS::$breadcrumb->append(ACP3_CMS::$lang->t('polls', 'polls'), ACP3_CMS::$uri->route('polls'))
			   ->append(ACP3_CMS::$lang->t('polls', 'vote'));

	// Wenn abgestimmt wurde
	if (!empty($_POST['answer']) && (is_array($_POST['answer']) === true || ACP3_Validate::isNumber($_POST['answer']) === true)) {
		$ip = $_SERVER['REMOTE_ADDR'];
		$answers = $_POST['answer'];

		// Überprüfen, ob der eingeloggte User schon abgestimmt hat
		if (ACP3_CMS::$auth->isUser() === true) {
			$query = ACP3_CMS::$db->countRows('poll_id', 'poll_votes', 'poll_id = \'' . ACP3_CMS::$uri->id . '\' AND user_id = \'' . ACP3_CMS::$auth->getUserId() . '\'');
		// Überprüfung für Gäste
		} else {
			$query = ACP3_CMS::$db->countRows('poll_id', 'poll_votes', 'poll_id = \'' . ACP3_CMS::$uri->id . '\' AND ip = \'' . $ip . '\'');
		}

		if ($query == 0) {
			$user_id = ACP3_CMS::$auth->isUser() ? ACP3_CMS::$auth->getUserId() : 0;

			if (is_array($answers) === true) {
				foreach ($answers as $answer) {
					if (ACP3_Validate::isNumber($answer) === true) {
						$insert_values = array(
							'poll_id' => ACP3_CMS::$uri->id,
							'answer_id' => $answer,
							'user_id' => $user_id,
							'ip' => $ip,
							'time' => $time,
						);
						ACP3_CMS::$db->insert('poll_votes', $insert_values);
					}
				}
				$bool = true;
			} else {
				$insert_values = array(
					'poll_id' => ACP3_CMS::$uri->id,
					'answer_id' => $answers,
					'user_id' => $user_id,
					'ip' => $ip,
					'time' => $time,
				);
				$bool = ACP3_CMS::$db->insert('poll_votes', $insert_values);
			}
			$text = $bool !== false ? ACP3_CMS::$lang->t('polls', 'poll_success') : ACP3_CMS::$lang->t('polls', 'poll_error');
		} else {
			$text = ACP3_CMS::$lang->t('polls', 'already_voted');
		}
		ACP3_CMS::setContent(confirmBox($text, ACP3_CMS::$uri->route('polls/result/id_' . ACP3_CMS::$uri->id)));
	} else {
		$question = ACP3_CMS::$db->select('question, multiple', 'polls', 'id = \'' . ACP3_CMS::$uri->id . '\'');
		$answers = ACP3_CMS::$db->select('id, text', 'poll_answers', 'poll_id = \'' . ACP3_CMS::$uri->id . '\'', 'id ASC');
		$c_answers = count($answers);

		$css_class = 'dark';
		for ($i = 0; $i < $c_answers; ++$i) {
			$css_class = $css_class == 'dark' ? 'light' : 'dark';
			$answers[$i]['css_class'] = $css_class;
			$answers[$i]['text'] = ACP3_CMS::$db->escape($answers[$i]['text'], 3);
		}

		ACP3_CMS::$view->assign('question', ACP3_CMS::$db->escape($question[0]['question'], 3));
		ACP3_CMS::$view->assign('multiple', $question[0]['multiple']);
		ACP3_CMS::$view->assign('answers', $answers);

		ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('polls/vote.tpl'));
	}
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}