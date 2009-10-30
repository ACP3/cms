<?php
/**
 * Polls
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

$polls = $db->select('id, start, end, question', 'poll_question', 0, 'start DESC, end DESC, id DESC', POS, CONFIG_ENTRIES);
$c_polls = count($polls);

if ($c_polls > 0) {
	$tpl->assign('pagination', pagination($db->countRows('*', 'poll_question')));

	for ($i = 0; $i < $c_polls; ++$i) {
		$polls[$i]['period'] = $date->period($polls[$i]['start'], $polls[$i]['end']);
		$polls[$i]['question'] = $polls[$i]['question'];
	}
	$tpl->assign('polls', $polls);
}
$content = $tpl->fetch('polls/adm_list.html');
