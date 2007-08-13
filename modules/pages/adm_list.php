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
if (isset($_POST['entries']) || isset($modules->gen['entries'])) {
	include 'modules/pages/entry.php';
} else {
	$pages = $db->query('SELECT p.id, p.start, p.end, p.mode, p.block_id, p.title, b.title AS block FROM ' . CONFIG_DB_PRE . 'pages AS p LEFT JOIN ' . CONFIG_DB_PRE . 'pages_blocks AS b ON p.block_id = b.id ORDER BY b.title ASC, p.sort ASC, p.title ASC LIMIT ' . POS . ', ' . CONFIG_ENTRIES);
	$c_pages = count($pages);

	if ($c_pages > 0) {
		$tpl->assign('pagination', pagination($db->query('SELECT p.id FROM ' . CONFIG_DB_PRE . 'pages AS p LEFT JOIN ' . CONFIG_DB_PRE . 'pages_blocks AS b ON p.block_id = b.id', 1)));

		$mode_replace = array(lang('pages', 'static_page'), lang('pages', 'dynamic_page'), lang('pages', 'hyperlink'));

		for($i = 0; $i < $c_pages; $i++) {
			$pages[$i]['start'] = date_aligned(1, $pages[$i]['start']);
			$pages[$i]['end'] = date_aligned(1, $pages[$i]['end']);
			$pages[$i]['mode'] = str_replace(array('1', '2', '3'), $mode_replace, $pages[$i]['mode']);
			$pages[$i]['block'] = $pages[$i]['block_id'] == '0' ? lang('pages', 'do_not_display') : $pages[$i]['block'];
			$pages[$i]['title'] = $pages[$i]['title'];
		}
		$tpl->assign('pages', $pages);

	}

	$content = $tpl->fetch('pages/adm_list.html');
}
?>