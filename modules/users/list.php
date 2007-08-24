<?php
if (!defined('IN_ACP3'))
	exit;

$users = $db->select('id, name, mail', 'users', 0, 'name ASC, id ASC', POS, CONFIG_ENTRIES);
$all_users = $db->select('id', 'users', 0, 0, 0, 0, 1);

if (count($users) > 0) {
	$tpl->assign('pagination', pagination($all_users));
	$tpl->assign('users', $users);
}
$tpl->assign('LANG_users_found', sprintf(lang('users', 'users_found'), $all_users));

$content = $tpl->fetch('users/list.html');
?>