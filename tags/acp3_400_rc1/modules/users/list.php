<?php
if (!defined('IN_ACP3'))
	exit;

$users = $db->select('id, nickname, realname, mail, website', 'users', 0, 'nickname ASC, id ASC', POS, CONFIG_ENTRIES);
$c_users = count($users);
$all_users = $db->select('id', 'users', 0, 0, 0, 0, 1);

if ($c_users > 0) {
	$tpl->assign('pagination', pagination($all_users));

	for ($i = 0; $i < $c_users; ++$i) {
		$users[$i]['website'] = $db->escape($users[$i]['website'], 3);
	}
	$tpl->assign('users', $users);
}
$tpl->assign('LANG_users_found', sprintf($lang->t('users', 'users_found'), $all_users));

$content = $tpl->fetch('users/list.html');
?>