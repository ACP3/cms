<?php
/**
 * Pages
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;

if (!empty($modules->id) && $db->select('id', 'pages_blocks', 'id = \'' . $modules->id . '\'', 0, 0, 0, 1) == '1') {
	$breadcrumb->assign(lang('pages', 'pages'), uri('acp/pages'));
	$breadcrumb->assign(lang('pages', 'acp_list_blocks'), uri('acp/pages/acp_list_blocks'));
	$breadcrumb->assign(lang('pages', 'edit_block'));

	if (isset($_POST['submit'])) {
		include 'modules/pages/entry.php';
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$block = $db->select('index_name, title', 'pages_blocks', 'id = \'' . $modules->id . '\'');

		$tpl->assign('form', isset($form) ? $form : $block[0]);

		$content = $tpl->fetch('pages/acp_edit_block.html');
	}
} else {
	redirect('errors/404');
}
?>