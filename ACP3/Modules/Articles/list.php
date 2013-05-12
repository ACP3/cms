<?php
/**
 * Articles
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

$period = 'start = end AND start <= :time OR start != end AND :time BETWEEN start AND end';
$time = ACP3\CMS::$injector['Date']->getCurrentDateTime();

$articles = ACP3\CMS::$injector['Db']->fetchAll('SELECT id, start, end, title FROM ' . DB_PRE . 'articles WHERE ' . $period . ' ORDER BY title ASC LIMIT ' . POS . ',' . ACP3\CMS::$injector['Auth']->entries, array('time' => $time));
$c_articles = count($articles);

if ($c_articles > 0) {
	ACP3\CMS::$injector['View']->assign('pagination', ACP3\Core\Functions::pagination(ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'articles WHERE ' . $period, array('time' => $time))));

	for ($i = 0; $i < $c_articles; ++$i) {
		$articles[$i]['date_formatted'] = ACP3\CMS::$injector['Date']->format($articles[$i]['start']);
		$articles[$i]['date_iso'] = ACP3\CMS::$injector['Date']->format($articles[$i]['start'], 'c');
	}

	ACP3\CMS::$injector['View']->assign('articles', $articles);
}