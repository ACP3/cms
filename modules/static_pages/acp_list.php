<?php
/**
 * Static Pages
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

getRedirectMessage();

$pages = $db->select('id, start, end, title', 'static_pages', 0, 'start DESC', POS, $auth->entries);
$c_pages = count($pages);

if ($c_pages > 0) {
	$tpl->assign('pagination', pagination($db->countRows('*', 'static_pages')));

	for ($i = 0; $i < $c_pages; ++$i) {
		$pages[$i]['period'] = $date->period($pages[$i]['start'], $pages[$i]['end']);
		$pages[$i]['title'] = $db->escape($pages[$i]['title'], 3);
	}
	$tpl->assign('pages', $pages);
	$tpl->assign('can_delete', ACP3_Modules::check('static_pages', 'acp_delete'));
}
ACP3_View::setContent(ACP3_View::fetchTemplate('static_pages/acp_list.tpl'));
