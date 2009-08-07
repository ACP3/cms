<?php
/**
 * News
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

$news = $db->select('n.id, n.start, n.end, n.headline, c.name AS cat', 'news AS n, ' . $db->prefix . 'categories AS c', 'n.category_id = c.id', 'n.start DESC, n.end DESC, n.id DESC', POS, CONFIG_ENTRIES);
$c_news = count($news);

if ($c_news > 0) {
	$tpl->assign('pagination', pagination($db->countRows('*', 'news')));

	for ($i = 0; $i < $c_news; ++$i) {
		$news[$i]['period'] = $date->period($news[$i]['start'], $news[$i]['end']);
	}
	$tpl->assign('news', $news);
}
$content = $tpl->fetch('news/adm_list.html');
?>