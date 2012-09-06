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

$polls = ACP3_CMS::$db2->fetchAll('SELECT p.id, p.start, p.end, p.question, COUNT(pv.poll_id) AS votes FROM ' . DB_PRE . 'polls AS p LEFT JOIN ' . DB_PRE . 'poll_votes AS pv ON(p.id = pv.poll_id) WHERE p.start <= ? GROUP BY p.id ORDER BY p.start DESC, p.end DESC, p.id DESC', array(ACP3_CMS::$date->getCurrentDateTime()));
$c_polls = count($polls);

if ($c_polls > 0) {
	for ($i = 0; $i < $c_polls; ++$i) {
		// Überprüfen, ob der eingeloggte User schon abgestimmt hat
		if (ACP3_CMS::$auth->isUser() === true) {
			$query = ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'poll_votes WHERE poll_id = ? AND user_id = ?', array($polls[$i]['id'], ACP3_CMS::$auth->getUserId()));
		// Überprüfung für Gäste
		} else {
			$query = ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'poll_votes WHERE poll_id = ? AND ip = ?', array($polls[$i]['id'], $_SERVER['REMOTE_ADDR']));
		}

		if ($query != 0 ||
			$polls[$i]['start'] != $polls[$i]['end'] && ACP3_CMS::$date->timestamp($polls[$i]['end']) <= ACP3_CMS::$date->timestamp()) {
			$polls[$i]['link'] = 'result';
		} else {
			$polls[$i]['link'] = 'vote';
		}
		$polls[$i]['date'] = $polls[$i]['start'] == $polls[$i]['end'] ? '-' : ACP3_CMS::$date->format($polls[$i]['end']);
	}
	ACP3_CMS::$view->assign('polls', $polls);
}

ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('polls/list.tpl'));
