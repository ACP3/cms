<?php
if (!defined('IN_FRONTEND'))
	exit;

$breadcrumb->assign(lang('users', 'users'), uri('users'));
$breadcrumb->assign(lang('users', 'view_profile'));

if (!empty($modules->id) && $db->select('id', 'users', 'id = \'' . $modules->id . '\'', 0, 0, 0, 1) == '1') {
	$user = $auth->getUserInfo('nickname, realname, mail, website', $modules->id);

	$user['website'] = $db->escape($user['website'], 3);

	$tpl->assign('user', $user);
}
$content = $tpl->fetch('users/view_profile.html');
?>