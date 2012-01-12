<?php
/**
 * News
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

$news = $db->select('n.id, n.start, n.end, n.headline, c.name AS cat', 'news AS n, {pre}categories AS c', 'n.category_id = c.id', 'n.start DESC, n.end DESC, n.id DESC', POS, $auth->entries);
$c_news = count($news);

if ($c_news > 0) {
	$tpl->assign('pagination', pagination($db->countRows('*', 'news')));

	for ($i = 0; $i < $c_news; ++$i) {
		$news[$i]['period'] = $date->period($news[$i]['start'], $news[$i]['end']);
		$news[$i]['headline'] = $db->escape($news[$i]['headline'], 3);
		$news[$i]['cat'] = $db->escape($news[$i]['cat'], 3);
	}
	$tpl->assign('news', $news);
}
$content = modules::fetchTemplate('news/adm_list.tpl');