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

if (isset($_POST['entries']) || isset($modules->gen['entries'])) {
	include 'modules/pages/entry.php';
} else {
	$breadcrumb->assign(lang('pages', 'pages'), uri('acp/pages'));
	$breadcrumb->assign(lang('pages', 'acp_list_blocks'));

	$blocks = $db->select('id, index_name, title', 'pages_blocks', 0, 'title ASC, index_name ASC', POS, CONFIG_ENTRIES);
	$c_blocks = count($blocks);

	if ($c_blocks > 0) {
		$tpl->assign('pagination', pagination($db->select('id', 'pages_blocks', 0, 0, 0, 0, 1)));
		$tpl->assign('blocks', $blocks);
	}

	$content = $tpl->fetch('pages/acp_list_blocks.html');
}
?>