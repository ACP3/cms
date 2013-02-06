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
$time = ACP3_CMS::$date->getCurrentDateTime();

$articles = ACP3_CMS::$db2->fetchAll('SELECT id, start, end, title FROM ' . DB_PRE . 'articles WHERE ' . $period . ' ORDER BY title ASC LIMIT ' . POS . ',' . ACP3_CMS::$auth->entries, array('time' => $time));
$c_articles = count($articles);

if ($c_articles > 0) {
	ACP3_CMS::$view->assign('pagination', pagination(ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'articles WHERE ' . $period, array('time' => $time))));

	for ($i = 0; $i < $c_articles; ++$i) {
		$articles[$i]['date_formatted'] = ACP3_CMS::$date->format($articles[$i]['start']);
		$articles[$i]['date_iso'] = ACP3_CMS::$date->format($articles[$i]['start'], 'c');
	}

	ACP3_CMS::$view->assign('articles', $articles);
}

ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('articles/list.tpl'));