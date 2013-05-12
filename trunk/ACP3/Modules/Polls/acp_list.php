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

ACP3\Core\Functions::getRedirectMessage();

$polls = ACP3\CMS::$injector['Db']->fetchAll('SELECT id, start, end, title FROM ' . DB_PRE . 'polls ORDER BY start DESC, end DESC, id DESC');
$c_polls = count($polls);

if ($c_polls > 0) {
	$can_delete = ACP3\Core\Modules::check('polls', 'acp_delete');
	$config = array(
		'element' => '#acp-table',
		'sort_col' => $can_delete === true ? 1 : 0,
		'sort_dir' => 'desc',
		'hide_col_sort' => $can_delete === true ? 0 : ''
	);
	ACP3\CMS::$injector['View']->appendContent(ACP3\Core\Functions::datatable($config));

	for ($i = 0; $i < $c_polls; ++$i) {
		$polls[$i]['period'] = ACP3\CMS::$injector['Date']->formatTimeRange($polls[$i]['start'], $polls[$i]['end']);
	}
	ACP3\CMS::$injector['View']->assign('polls', $polls);
	ACP3\CMS::$injector['View']->assign('can_delete', $can_delete);
}