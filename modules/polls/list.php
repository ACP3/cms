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

$polls = $db->select('id, start, end, question', 'polls', 'start <= \'' . $date->timestamp() . '\'', 'start DESC, end DESC, id DESC');
$c_polls = count($polls);

if ($c_polls > 0) {
	for ($i = 0; $i < $c_polls; ++$i) {
		$polls[$i]['question'] = $db->escape($polls[$i]['question'], 3);

		// Überprüfen, ob der eingeloogter User schon abgestimmt hat
		if ($auth->isUser()) {
			$query = $db->countRows('poll_id', 'poll_votes', 'poll_id = \'' . $polls[$i]['id'] . '\' AND user_id = \'' . $auth->getUserId() . '\'');
		// Überprüfung für Gäste
		} else {
			$query = $db->countRows('poll_id', 'poll_votes', 'poll_id = \'' . $polls[$i]['id'] . '\' AND ip = \'' . $_SERVER['REMOTE_ADDR'] . '\'');
		}

		if ($query != 0 || $polls[$i]['start'] != $polls[$i]['end'] && $polls[$i]['end'] <= $date->timestamp()) {
			$polls[$i]['link'] = 'result';
		} else {
			$polls[$i]['link'] = 'vote';
		}
		$polls[$i]['votes'] = $db->countRows('poll_id', 'poll_votes', 'poll_id = \'' . $polls[$i]['id'] . '\'');
		$polls[$i]['date'] = $polls[$i]['start'] == $polls[$i]['end'] ? '-' : $date->format($polls[$i]['end']);
	}
	$tpl->assign('polls', $polls);
}

$content = modules::fetchTemplate('polls/list.html');
