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
$pages_list = pagesList(2);

if (count($pages_list) > 0) {
	$mode_replace = array($lang->t('pages', 'static_page'), $lang->t('pages', 'dynamic_page'), $lang->t('pages', 'hyperlink'));

	$i = 0;
	foreach ($pages_list as $block => $pages) {
		foreach ($pages as $row) {
			$pages_list[$block][$i]['start'] = $date->format($row['start']);
			$pages_list[$block][$i]['end'] = $date->format($row['end']);
			$pages_list[$block][$i]['mode'] = str_replace(array('1', '2', '3'), $mode_replace, $row['mode']);
			$i++;
		}
	}
	$tpl->assign('pages_list', $pages_list);
}
$content = $tpl->fetch('pages/adm_list.html');
?>