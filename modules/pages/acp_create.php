<?php
/**
 * Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP'))
	exit;

if (isset($_POST['submit'])) {
	include 'modules/pages/entry.php';
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	//Funktionen einbinden
	include_once 'modules/pages/functions.php';

	// Datumsauswahl
	$tpl->assign('start_date', publication_period('start'));
	$tpl->assign('end_date', publication_period('end'));

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

	$content = $tpl->fetch('pages/acp_create.html');
}
?>