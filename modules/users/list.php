<?php
if (defined('IN_ACP3') === false)
	exit;

$users = ACP3_CMS::$db->select('id, nickname, realname, mail, website', 'users', 0, 'nickname ASC, id ASC', POS, ACP3_CMS::$auth->entries);
$c_users = count($users);
$all_users = ACP3_CMS::$db->countRows('*', 'users');

if ($c_users > 0) {
	ACP3_CMS::$view->assign('pagination', pagination($all_users));

	for ($i = 0; $i < $c_users; ++$i) {
		$pos = strrpos($users[$i]['realname'], ':');
		$users[$i]['realname_display'] = substr($users[$i]['realname'], $pos + 1);
		$users[$i]['realname'] = substr(ACP3_CMS::$db->escape($users[$i]['realname'], 3), 0, $pos);
		$pos = strrpos($users[$i]['mail'], ':');
		$users[$i]['mail_display'] = substr($users[$i]['mail'], $pos + 1);
		$users[$i]['mail'] = substr($users[$i]['mail'], 0, $pos);
		$pos = strrpos($users[$i]['website'], ':');
		$users[$i]['website_display'] = substr($users[$i]['website'], $pos + 1);
		$users[$i]['website'] = substr(ACP3_CMS::$db->escape($users[$i]['website'], 3), 0, $pos);
	}
	ACP3_CMS::$view->assign('users', $users);
}
ACP3_CMS::$view->assign('LANG_users_found', sprintf(ACP3_CMS::$lang->t('users', 'users_found'), $all_users));

ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('users/list.tpl'));
