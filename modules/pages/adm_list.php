<?php
/**
 * Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

include_once ACP3_ROOT . 'modules/pages/functions.php';
$pages = pagesList();
$c_pages = count($pages);

if ($c_pages > 0) {
	$replace = array($lang->t('pages', 'static_page'), $lang->t('pages', 'dynamic_page'), $lang->t('pages', 'hyperlink'));

	$i = 0;
	foreach ($pages as $block => $page) {
		foreach ($page as $row) {
			$pages[$block][$i]['start'] = $date->format($page[$i]['start']);
			$pages[$block][$i]['end'] = $date->format($page[$i]['end']);
			$pages[$block][$i]['mode'] = str_replace(array('1', '2', '3'), $replace, $page[$i]['mode']);
			$i++;
		}
	}
	$tpl->assign('pages', $pages);
}
$content = $tpl->fetch('pages/adm_list.html');
?>