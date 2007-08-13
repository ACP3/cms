<?php
/**
 * Guestbook
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;

if (isset($_POST['submit'])) {
	include 'modules/gb/entry.php';
}
if (!isset($_POST['submit']) || isset($error_msg)) {
	$tpl->assign('error_msg', isset($error_msg) ? $error_msg : '');

	// Emoticons einbinden
	if ($modules->check(1, 'emoticons', 'info')) {
		include_once 'modules/emoticons/functions.php';
		$tpl->assign('emoticons', emoticons_list());
	}

	$breadcrumb->assign(lang('gb', 'gb'), uri('gb'));
	$breadcrumb->assign(lang('gb', 'create'));

	$tpl->assign('form', isset($form) ? $form : '');

	$content = $tpl->fetch('gb/create.html');
}
?>