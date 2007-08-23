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

if (!empty($modules->id) && $db->select('id', 'pages', 'id = \'' . $modules->id . '\'', 0, 0, 0, 1) == '1') {
	if (isset($_POST['submit'])) {
		include 'modules/pages/entry.php';
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		//Funktionen einbinden
		include_once 'modules/pages/functions.php';

		$page = $db->select('start, end, mode, parent, block_id, sort, title, uri, target, text', 'pages', 'id = \'' . $modules->id . '\'');
		$page[0]['text'] = $db->escape($page[0]['text'], 3);
		$page[0]['uri'] = $db->escape($page[0]['uri'], 3);
		// Datum
		$start_date = explode('.', date('j.n.Y.G.i', $page[0]['start']));
		$end_date = explode('.', date('j.n.Y.G.i', $page[0]['end']));

		// Datumsauswahl
		$tpl->assign('start_day', date_dropdown('day', 'start_day', 'start_day', $start_date[0]));
		$tpl->assign('start_month', date_dropdown('month', 'start_month', 'start_month', $start_date[1]));
		$tpl->assign('start_year', date_dropdown('year', 'start_year', 'start_year', $start_date[2]));
		$tpl->assign('start_hour', date_dropdown('hour', 'start_hour', 'start_hour', $start_date[3]));
		$tpl->assign('start_min', date_dropdown('min', 'start_min', 'start_min', $start_date[4]));
		$tpl->assign('end_day', date_dropdown('day', 'end_day', 'end_day', $end_date[0]));
		$tpl->assign('end_month', date_dropdown('month', 'end_month', 'end_month', $end_date[1]));
		$tpl->assign('end_year', date_dropdown('year', 'end_year', 'end_year', $end_date[2]));
		$tpl->assign('end_hour', date_dropdown('hour', 'end_hour', 'end_hour', $end_date[3]));
		$tpl->assign('end_min', date_dropdown('min', 'end_min', 'end_min', $end_date[4]));

		$mode[0]['value'] = 1;
		$mode[0]['selected'] = select_entry('mode', '1', $page[0]['mode']);
		$mode[0]['lang'] = lang('pages', 'static_page');
		$mode[1]['value'] = 2;
		$mode[1]['selected'] = select_entry('mode', '2', $page[0]['mode']);
		$mode[1]['lang'] = lang('pages', 'dynamic_page');
		$mode[2]['value'] = 3;
		$mode[2]['selected'] = select_entry('mode', '3', $page[0]['mode']);
		$mode[2]['lang'] = lang('pages', 'hyperlink');
		$tpl->assign('mode', $mode);

		$blocks = $db->select('id, title', 'pages_blocks', 0, 'title ASC, id ASC');
		$c_blocks = count($blocks);

		for ($i = 0; $i < $c_blocks; $i++) {
			$blocks[$i]['selected'] = select_entry('blocks', $blocks[$i]['id'], $page[0]['block_id']);
		}
		$blocks[$c_blocks]['id'] = '0';
		$blocks[$c_blocks]['index_name'] = 'dot_display';
		$blocks[$c_blocks]['selected'] = select_entry('block', '0', $page[0]['block_id']);
		$blocks[$c_blocks]['title'] = lang('pages', 'do_not_display');
		$tpl->assign('blocks', $blocks);

		$target[0]['value'] = 1;
		$target[0]['selected'] = select_entry('target', '1', $page[0]['target']);
		$target[0]['lang'] = lang('pages', 'window_self');
		$target[1]['value'] = 2;
		$target[1]['selected'] = select_entry('target', '2', $page[0]['target']);
		$target[1]['lang'] = lang('pages', 'window_blank');
		$tpl->assign('target', $target);

		$tpl->assign('form', isset($form) ? $form : $page[0]);

		$tpl->assign('pages_list', pages_list(0, $page[0]['parent']));

		$content = $tpl->fetch('pages/edit.html');
	}
} else {
	redirect('errors/404');
}
?>