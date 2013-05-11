<?php
/**
 * Articles
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

getRedirectMessage();

$articles = ACP3_CMS::$db2->fetchAll('SELECT id, start, end, title FROM ' . DB_PRE . 'articles ORDER BY title ASC');
$c_articles = count($articles);

if ($c_articles > 0) {
	$can_delete = ACP3_Modules::check('articles', 'acp_delete');
	$config = array(
		'element' => '#acp-table',
		'sort_col' => $can_delete === true ? 2 : 1,
		'sort_dir' => 'asc',
		'hide_col_sort' => $can_delete === true ? 0 : ''
	);
	ACP3_CMS::$view->appendContent(datatable($config));
	for ($i = 0; $i < $c_articles; ++$i) {
		$articles[$i]['period'] = ACP3_CMS::$date->formatTimeRange($articles[$i]['start'], $articles[$i]['end']);
	}
	ACP3_CMS::$view->assign('articles', $articles);
	ACP3_CMS::$view->assign('can_delete', $can_delete);
}