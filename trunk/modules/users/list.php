<?php
if (!defined('IN_ACP3'))
	exit;

$users = $db->select('id, name, mail', 'users', 0, 'name ASC, id ASC', POS, CONFIG_ENTRIES);
$c_users = count($users);

if ($c_users > 0) {
	$tpl->assign('pagination', pagination($db->select('id', 'users', 0, 0, 0, 0, 1)));

	$tpl->assign('users', $users);
}

$tpl->assign('LANG_users_found', sprintf(lang('users', 'users_found'), $c_users));

$content = $tpl->fetch('users/list.html');
?>