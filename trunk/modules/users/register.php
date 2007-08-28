<?php
if (!defined('IN_ACP3'))
	exit;

if ($auth->is_user()) {
	redirect(0, ROOT_DIR);
} else {
	$breadcrumb->assign(lang('users', 'users'), uri('users'));
	$breadcrumb->assign(lang('users', 'register'));

	if (isset($_POST['submit'])) {
		include 'modules/users/entry.php';
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$tpl->assign('form', isset($form) ? $form : '');

		$content = $tpl->fetch('users/register.html');
	}
}
?>