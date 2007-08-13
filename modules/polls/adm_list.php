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
if (!$modules->check())
	redirect('errors/403');
if (isset($_POST['entries']) || isset($modules->gen['entries'])) {
	include 'modules/polls/entry.php';
} else {
	$polls = $db->select('id, start, end, question', 'poll_question', 0, 'start DESC', POS, CONFIG_ENTRIES);
	$c_polls = count($polls);

	if ($c_polls > 0) {
		$tpl->assign('pagination', pagination($db->select('id', 'poll_question', 0, 0, 0, 0, 1)));

		for ($i = 0; $i < $c_polls; $i++) {
			$polls[$i]['start'] = date_aligned(1, $polls[$i]['start']);
			$polls[$i]['end'] = date_aligned(1, $polls[$i]['end']);
			$polls[$i]['question'] = $polls[$i]['question'];
		}
		$tpl->assign('polls', $polls);
	}
	$content = $tpl->fetch('polls/adm_list.html');
}
?>