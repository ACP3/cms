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

if (!empty($modules->id) && $db->select('id', 'pages_blocks', 'id = \'' . $modules->id . '\'', 0, 0, 0, 1) == '1') {
	if (isset($_POST['submit'])) {
		include 'modules/pages/entry.php';
	}
	if (!isset($_POST['submit']) || isset($error_msg)) {
		$tpl->assign('error_msg', isset($error_msg) ? $error_msg : '');

		$block = $db->select('index_name, title', 'pages_blocks', 'id = \'' . $modules->id . '\'');

		$tpl->assign('form', isset($form) ? $form : $block[0]);

		$content = $tpl->fetch('pages/edit_block.html');
	}
} else {
	redirect('errors/404');
}
?>