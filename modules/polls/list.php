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

$polls = ACP3_CMS::$db->select('id, start, end, question', 'polls', 'start <= \'' . ACP3_CMS::$date->getCurrentDateTime() . '\'', 'start DESC, end DESC, id DESC');
$c_polls = count($polls);

if ($c_polls > 0) {
	for ($i = 0; $i < $c_polls; ++$i) {
		$polls[$i]['question'] = ACP3_CMS::$db->escape($polls[$i]['question'], 3);

		// Überprüfen, ob der eingeloogter User schon abgestimmt hat
		if (ACP3_CMS::$auth->isUser() === true) {
			$query = ACP3_CMS::$db->countRows('poll_id', 'poll_votes', 'poll_id = \'' . $polls[$i]['id'] . '\' AND user_id = \'' . ACP3_CMS::$auth->getUserId() . '\'');
		// Überprüfung für Gäste
		} else {
			$query = ACP3_CMS::$db->countRows('poll_id', 'poll_votes', 'poll_id = \'' . $polls[$i]['id'] . '\' AND ip = \'' . $_SERVER['REMOTE_ADDR'] . '\'');
		}

		if ($query != 0 || $polls[$i]['start'] != $polls[$i]['end'] && ACP3_CMS::$date->timestamp($polls[$i]['end']) <= ACP3_CMS::$date->timestamp()) {
			$polls[$i]['link'] = 'result';
		} else {
			$polls[$i]['link'] = 'vote';
		}
		$polls[$i]['votes'] = ACP3_CMS::$db->countRows('poll_id', 'poll_votes', 'poll_id = \'' . $polls[$i]['id'] . '\'');
		$polls[$i]['date'] = $polls[$i]['start'] == $polls[$i]['end'] ? '-' : ACP3_CMS::$date->format($polls[$i]['end']);
	}
	ACP3_CMS::$view->assign('polls', $polls);
}

ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('polls/list.tpl'));
