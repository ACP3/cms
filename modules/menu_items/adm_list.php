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

$pages_list = pagesList(2);

if (count($pages_list) > 0) {
	$mode_replace = array($lang->t('menu_items', 'module'), $lang->t('menu_items', 'dynamic_page'), $lang->t('menu_items', 'hyperlink'));

	$i = 0;
	foreach ($pages_list as $block => $pages) {
		foreach ($pages as $row) {
			$pages_list[$block][$i]['period'] = $date->period($row['start'], $row['end']);
			$pages_list[$block][$i]['mode'] = str_replace(array('1', '2', '3'), $mode_replace, $row['mode']);
			$i++;
		}
	}
	$tpl->assign('pages_list', $pages_list);
}
$content = $tpl->fetch('menu_items/adm_list.html');
?>