<?php
if (!defined('IN_ACP3'))
	exit;

if (defined('IS_USER')) {
	redirect(0, ROOT_DIR);
} else {
	if (isset($_POST['submit'])) {
		include 'modules/users/entry.php';
	}
	if (!isset($_POST['submit']) || isset($error_msg)) {
		$tpl->assign('error_msg', isset($error_msg) ? $error_msg : '');

		$tpl->assign('form', isset($form) ? $form : '');

		$content = $tpl->fetch('users/forgot_pwd.html');
	}
}
?>