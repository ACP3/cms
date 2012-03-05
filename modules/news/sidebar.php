<?php
/**
 * News
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */
if (defined('IN_ACP3') === false)
	exit;

$settings = ACP3_Config::getModuleSettings('news');

$time = $date->timestamp();
$where = 'start = end AND start <= ' . $time . ' OR start != end AND start <= ' . $time . ' AND end >= ' . $time;
$news = $db->select('id, start, headline', 'news', $where, 'start DESC, end DESC, id DESC', $settings['sidebar']);
$c_news = count($news);

if ($c_news > 0) {
	$settings = ACP3_Config::getModuleSettings('news');

	for ($i = 0; $i < $c_news; ++$i) {
		$news[$i]['start'] = $date->format($news[$i]['start'], $settings['dateformat']);
		$news[$i]['headline'] = $db->escape($news[$i]['headline'], 3);
		$news[$i]['headline_short'] = shortenEntry($news[$i]['headline'], 30, 5, '...');
	}
	$tpl->assign('sidebar_news', $news);
}

ACP3_View::displayTemplate('news/sidebar.tpl');