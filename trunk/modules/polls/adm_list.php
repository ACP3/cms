<?php
/**
 * Polls
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

getRedirectMessage();

$polls = $db->select('id, start, end, question', 'polls', 0, 'start DESC, end DESC, id DESC', POS, $auth->entries);
$c_polls = count($polls);

if ($c_polls > 0) {
	$tpl->assign('pagination', pagination($db->countRows('*', 'polls')));

	for ($i = 0; $i < $c_polls; ++$i) {
		$polls[$i]['period'] = $date->period($polls[$i]['start'], $polls[$i]['end']);
		$polls[$i]['question'] = $db->escape($polls[$i]['question'], 3);
	}
	$tpl->assign('polls', $polls);
}
ACP3_View::setContent(ACP3_View::fetchTemplate('polls/adm_list.tpl'));
