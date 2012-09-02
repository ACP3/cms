<?php
/**
 * Polls
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

getRedirectMessage();

$polls = ACP3_CMS::$db->select('id, start, end, question', 'polls', 0, 'start DESC, end DESC, id DESC', POS, ACP3_CMS::$auth->entries);
$c_polls = count($polls);

if ($c_polls > 0) {
	ACP3_CMS::$view->assign('pagination', pagination(ACP3_CMS::$db->countRows('*', 'polls')));

	for ($i = 0; $i < $c_polls; ++$i) {
		$polls[$i]['period'] = ACP3_CMS::$date->period($polls[$i]['start'], $polls[$i]['end']);
		$polls[$i]['question'] = ACP3_CMS::$db->escape($polls[$i]['question'], 3);
	}
	ACP3_CMS::$view->assign('polls', $polls);
	ACP3_CMS::$view->assign('can_delete', ACP3_Modules::check('polls', 'acp_delete'));
}
ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('polls/acp_list.tpl'));
