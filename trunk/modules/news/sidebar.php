<?php
/**
 * News
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */
if (defined('IN_ACP3') === false)
	exit;

$settings = ACP3_Config::getSettings('news');

$where = 'start = end AND start <= :time OR start != end AND :time BETWEEN start AND end';
$news = ACP3_CMS::$db2->fetchAll('SELECT id, start, headline FROM ' . DB_PRE . 'news WHERE ' . $where . ' ORDER BY start DESC, end DESC, id DESC LIMIT ' . $settings['sidebar'], array('time' => ACP3_CMS::$date->getCurrentDateTime()));
$c_news = count($news);

if ($c_news > 0) {
	for ($i = 0; $i < $c_news; ++$i) {
		$news[$i]['start'] = ACP3_CMS::$date->format($news[$i]['start'], $settings['dateformat']);
		$news[$i]['headline'] = $news[$i]['headline'];
		$news[$i]['headline_short'] = shortenEntry($news[$i]['headline'], 30, 5, '...');
	}
	ACP3_CMS::$view->assign('sidebar_news', $news);
}

ACP3_CMS::$view->displayTemplate('news/sidebar.tpl');