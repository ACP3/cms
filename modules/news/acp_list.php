<?php
/**
 * News
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

getRedirectMessage();

$news = ACP3_CMS::$db2->fetchAll('SELECT n.id, n.start, n.end, n.title, c.title AS cat FROM ' . DB_PRE . 'news AS n, ' . DB_PRE . 'categories AS c WHERE n.category_id = c.id ORDER BY n.start DESC, n.end DESC, n.id DESC');
$c_news = count($news);

if ($c_news > 0) {
	$can_delete = ACP3_Modules::check('news', 'acp_delete');
	$config = array(
		'element' => '#acp-table',
		'hide_col_sort' => $can_delete === true ? 0 : ''
	);
	ACP3_CMS::setContent(datatable($config));

	for ($i = 0; $i < $c_news; ++$i) {
		$news[$i]['period'] = ACP3_CMS::$date->period($news[$i]['start'], $news[$i]['end']);
	}
	ACP3_CMS::$view->assign('news', $news);
	ACP3_CMS::$view->assign('can_delete', $can_delete);
}
ACP3_CMS::appendContent(ACP3_CMS::$view->fetchTemplate('news/acp_list.tpl'));