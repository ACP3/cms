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
if (!$modules->check())
	redirect('errors/403');
if (isset($_POST['submit'])) {
	include 'modules/pages/entry.php';
}
if (!isset($_POST['submit']) || isset($error_msg)) {
	//Funktionen einbinden
	include_once 'modules/pages/functions.php';

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

	$mode[0]['value'] = 1;
	$mode[0]['selected'] = select_entry('mode', '1');
	$mode[0]['lang'] = lang('pages', 'static_page');
	$mode[1]['value'] = 2;
	$mode[1]['selected'] = select_entry('mode', '2');
	$mode[1]['lang'] = lang('pages', 'dynamic_page');
	$mode[2]['value'] = 3;
	$mode[2]['selected'] = select_entry('mode', '3');
	$mode[2]['lang'] = lang('pages', 'hyperlink');
	$tpl->assign('mode', $mode);

	$blocks = $db->select('id, title', 'pages_blocks');
	$c_blocks = count($blocks);

	for ($i = 0; $i < $c_blocks; $i++) {
		$blocks[$i]['selected'] = select_entry('block', $blocks[$i]['id']);
	}
	$blocks[$c_blocks]['id'] = 0;
	$blocks[$c_blocks]['index_name'] = 'dot_display';
	$blocks[$c_blocks]['selected'] = select_entry('blocks', '0');
	$blocks[$c_blocks]['title'] = lang('pages', 'do_not_display');
	$tpl->assign('blocks', $blocks);

	$target[0]['value'] = 1;
	$target[0]['selected'] = select_entry('target', '1');
	$target[0]['lang'] = lang('pages', 'window_self');
	$target[1]['value'] = 2;
	$target[1]['selected'] = select_entry('target', '2');
	$target[1]['lang'] = lang('pages', 'window_blank');
	$tpl->assign('target', $target);

	$tpl->assign('form', isset($form) ? $form : '');

	$tpl->assign('pages_list', pages_list());

	$content = $tpl->fetch('pages/create.html');
}
?>