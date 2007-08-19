<?php
/**
 * News
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (isset($_POST['submit'])) {
	include 'modules/news/entry.php';
}
if (!isset($_POST['submit']) || isset($error_msg)) {
	$tpl->assign('error_msg', isset($error_msg) ? $error_msg : '');

	// Datumsauswahl
	$tpl->assign('start_day', date_dropdown('day', 'start_day', 'start_day'));
	$tpl->assign('start_month', date_dropdown('month', 'start_month', 'start_month'));
	$tpl->assign('start_year', date_dropdown('year', 'start_year', 'start_year'));
	$tpl->assign('start_hour', date_dropdown('hour', 'start_hour', 'start_hour'));
	$tpl->assign('start_min', date_dropdown('min', 'start_min', 'start_min'));
	$tpl->assign('end_day', date_dropdown('day', 'end_day', 'end_day'));
	$tpl->assign('end_month', date_dropdown('month', 'end_month', 'end_month'));
	$tpl->assign('end_year', date_dropdown('year', 'end_year', 'end_year'));
	$tpl->assign('end_hour', date_dropdown('hour', 'end_hour', 'end_hour'));
	$tpl->assign('end_min', date_dropdown('min', 'end_min', 'end_min'));

	// Kategorien
	if (!$cache->check('categories_news')) {
		$cache->create('categories_news', $db->select('id, name, description', 'categories', 'module = \'news\'', 'name ASC'));
	}
	$categories = $cache->output('categories_news');
	$c_categories = count($categories);

	if ($c_categories > 0) {
		for ($i = 0; $i < $c_categories; $i++) {
			$categories[$i]['selected'] = select_entry('cat', $categories[$i]['id']);
			$categories[$i]['name'] = $categories[$i]['name'];
		}
		$tpl->assign('categories', $categories);
	}

	// Linkziel
	$target[0]['value'] = '1';
	$target[0]['selected'] = select_entry('target', '1');
	$target[0]['lang'] = lang('news', 'window_self');
	$target[1]['value'] = '2';
	$target[1]['selected'] = select_entry('target', '2');
	$target[1]['lang'] = lang('news', 'window_blank');
	$tpl->assign('target', $target);

	$tpl->assign('form', isset($form) ? $form : '');

	$content = $tpl->fetch('news/create.html');
}
?>