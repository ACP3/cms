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

$pages = $db->select('id, start, end, title', 'static_pages', 0, 'start DESC', POS, CONFIG_ENTRIES);
$c_pages = count($pages);

if ($c_pages > 0) {
	$tpl->assign('pagination', pagination($db->select('COUNT(id)', 'static_pages', 0, 0, 0, 0, 1)));

	for ($i = 0; $i < $c_pages; ++$i) {
		$pages[$i]['period'] = $date->period($pages[$i]['start'], $pages[$i]['end']);
		$pages[$i]['title'] = $db->escape($pages[$i]['title'], 3);
	}
	$tpl->assign('pages', $pages);
}
$content = $tpl->fetch('static_pages/adm_list.html');
?>