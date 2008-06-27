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
	$tpl->assign('pagination', pagination($c_pages));

	$mode_replace = array($lang->t('pages', 'static_page'), $lang->t('pages', 'dynamic_page'), $lang->t('pages', 'hyperlink'));

	// Einträge pro Seite
	$epp = POS + CONFIG_ENTRIES <= $c_pages ? CONFIG_ENTRIES : $c_pages;
	$output = array();
	for ($i = POS; $i < $epp; ++$i) {
		$output[$i]['id'] = $pages[$i]['id'];
		$output[$i]['start'] = $date->format($pages[$i]['start']);
		$output[$i]['end'] = $date->format($pages[$i]['end']);
		$output[$i]['mode'] = str_replace(array('1', '2', '3'), $mode_replace, $pages[$i]['mode']);
		$output[$i]['block'] = $pages[$i]['block_id'] == '0' ? $lang->t('pages', 'do_not_display') : $pages[$i]['block_title'];
		$output[$i]['title'] = $pages[$i]['title'];
	}
	$tpl->assign('pages', $output);
}
$content = $tpl->fetch('pages/adm_list.html');
?>