<?php
if (!defined('IN_ACP3'))
	exit;

$breadcrumb->assign(lang('users', 'users'), uri('users'));
$breadcrumb->assign(lang('users', 'view_profile'));

if (!empty($modules->id) && $db->select('id', 'users', 'id = \'' . $modules->id . '\'', 0, 0, 0, 1) == '1') {
	$user = $db->select('name, mail', 'users', 'id = \'' . $modules->id . '\'');

	$tpl->assign('user', $user[0]);
}
$content = $tpl->fetch('users/view_profile.html');
?>