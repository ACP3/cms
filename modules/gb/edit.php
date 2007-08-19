<?php
/**
 * Guestbook
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (!empty($modules->id) && $db->select('id', 'gb', 'id = \'' . $modules->id . '\'', 0, 0, 0, 1) == '1') {
	if (isset($_POST['submit'])) {
		include 'modules/gb/entry.php';
	}
	if (!isset($_POST['submit']) || isset($error_msg)) {
		$tpl->assign('error_msg', isset($error_msg) ? $error_msg : '');

		$gb = $db->select('name, message', 'gb', 'id = \'' . $modules->id . '\'');

		$tpl->assign('form', isset($form) ? $form : $gb[0]);

		$content = $tpl->fetch('gb/edit.html');
	}
} else {
	redirect('errors/404');
}
?>