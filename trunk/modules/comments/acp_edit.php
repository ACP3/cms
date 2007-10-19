<?php
/**
 * Comments
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP'))
	exit;

if (!empty($modules->id) && $db->select('id', 'comments', 'id = \'' . $modules->id . '\'', 0, 0, 0, 1) == '1') {
	$comment = $db->select('name, message, module', 'comments', 'id = \'' . $modules->id . '\'');

	$comment[0]['module'] = $db->escape($comment[0]['module'], 3);
	$breadcrumb->assign(lang('comments', 'comments'), uri('acp/comments'));
	$breadcrumb->assign(lang($comment[0]['module'], $comment[0]['module']), uri('acp/comments/acp_list/module_' . $comment[0]['module']));
	$breadcrumb->assign(lang('comments', 'edit'));
	unset($comment[0]['module']);

	if (isset($_POST['submit'])) {
		include('modules/comments/entry.php');
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$tpl->assign('form', isset($form) ? $form : $comment[0]);

		$content = $tpl->fetch('comments/acp_edit.html');
	}
} else {
	redirect('errors/404');
}
?>