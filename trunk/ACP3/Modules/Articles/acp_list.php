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

ACP3\Core\Functions::getRedirectMessage();

$articles = ACP3\CMS::$injector['Db']->fetchAll('SELECT id, start, end, title FROM ' . DB_PRE . 'articles ORDER BY title ASC');
$c_articles = count($articles);

if ($c_articles > 0) {
	$can_delete = ACP3\Core\Modules::check('articles', 'acp_delete');
	$config = array(
		'element' => '#acp-table',
		'sort_col' => $can_delete === true ? 2 : 1,
		'sort_dir' => 'asc',
		'hide_col_sort' => $can_delete === true ? 0 : ''
	);
	ACP3\CMS::$injector['View']->appendContent(ACP3\Core\Functions::datatable($config));
	for ($i = 0; $i < $c_articles; ++$i) {
		$articles[$i]['period'] = ACP3\CMS::$injector['Date']->formatTimeRange($articles[$i]['start'], $articles[$i]['end']);
	}
	ACP3\CMS::$injector['View']->assign('articles', $articles);
	ACP3\CMS::$injector['View']->assign('can_delete', $can_delete);
}