<?php
if (!defined('IN_ACP3'))
	exit;

if (!$auth->is_user() || !preg_match('/\d/', $_SESSION['acp3_id'])) {
	redirect('errors/403');
} else {
	$breadcrumb->assign(lang('users', 'users'), uri('users'));
	$breadcrumb->assign(lang('users', 'home'));

	if (isset($_POST['submit'])) {
		include 'modules/users/entry.php';
	}
	if (!isset($_POST['submit'])) {
		$user = $db->select('draft', 'users', 'id = \'' . $_SESSION['acp3_id'] . '\'');

		$tpl->assign('draft', $db->escape($user[0]['draft'], 3));

		$content = $tpl->fetch('users/home.html');
	}
}
?>