<?php
/**
 * News
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */
if (!defined('IN_ACP3') && !defined('IN_ADM'))
	exit;

$time = $date->timestamp();
$where = 'start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\'';
$news = $db->select('id, start, headline', 'news', $where, 'start DESC', 5);
$c_news = count($news);

if ($c_news > 0) {
	for ($i = 0; $i < $c_news; ++$i) {
		$news[$i]['start'] = $date->format($news[$i]['start']);
		$news[$i]['headline_short'] = shortenEntry($db->escape($news[$i]['headline'], 3), 30, 5, '...');
	}
	$tpl->assign('sidebar_news', $news);
}

$tpl->display('news/sidebar.html');
?>