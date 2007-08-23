<?php
if (!defined('IN_ACP3'))
	exit;

if (defined('IS_USER')) {
	redirect(0, ROOT_DIR);
} else {
	if (isset($_POST['submit'])) {
		include 'modules/users/entry.php';
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$tpl->assign('form', isset($form) ? $form : '');

		$content = $tpl->fetch('users/forgot_pwd.html');
	}
}
?>