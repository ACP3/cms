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

$news = ACP3_CMS::$db->select('n.id, n.start, n.end, n.headline, c.name AS cat', 'news AS n, {pre}categories AS c', 'n.category_id = c.id', 'n.start DESC, n.end DESC, n.id DESC', POS, ACP3_CMS::$auth->entries);
$c_news = count($news);

if ($c_news > 0) {
	ACP3_CMS::$view->assign('pagination', pagination(ACP3_CMS::$db->countRows('*', 'news')));

	for ($i = 0; $i < $c_news; ++$i) {
		$news[$i]['period'] = ACP3_CMS::$date->period($news[$i]['start'], $news[$i]['end']);
		$news[$i]['headline'] = ACP3_CMS::$db->escape($news[$i]['headline'], 3);
		$news[$i]['cat'] = ACP3_CMS::$db->escape($news[$i]['cat'], 3);
	}
	ACP3_CMS::$view->assign('news', $news);
	ACP3_CMS::$view->assign('can_delete', ACP3_Modules::check('news', 'acp_delete'));
}
ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('news/acp_list.tpl'));