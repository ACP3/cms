<?php
/**
 * Static Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

$pages = $db->select('id, start, end, title', 'static_pages', 0, 'start DESC', POS, $auth->entries);
$c_pages = count($pages);

if ($c_pages > 0) {
	$tpl->assign('pagination', pagination($db->countRows('*', 'static_pages')));

	for ($i = 0; $i < $c_pages; ++$i) {
		$pages[$i]['period'] = $date->period($pages[$i]['start'], $pages[$i]['end']);
	}
	$tpl->assign('pages', $pages);
}
$content = modules::fetchTemplate('static_pages/adm_list.html');
