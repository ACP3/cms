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

getRedirectMessage();

$pages = ACP3_CMS::$db->select('id, start, end, title', 'articles', 0, 'title ASC', POS, ACP3_CMS::$auth->entries);
$c_pages = count($pages);

if ($c_pages > 0) {
	ACP3_CMS::$view->assign('pagination', pagination(ACP3_CMS::$db->countRows('*', 'articles')));

	for ($i = 0; $i < $c_pages; ++$i) {
		$pages[$i]['period'] = ACP3_CMS::$date->period($pages[$i]['start'], $pages[$i]['end']);
		$pages[$i]['title'] = ACP3_CMS::$db->escape($pages[$i]['title'], 3);
	}
	ACP3_CMS::$view->assign('pages', $pages);
	ACP3_CMS::$view->assign('can_delete', ACP3_Modules::check('articles', 'acp_delete'));
}
ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('articles/acp_list.tpl'));
