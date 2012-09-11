<?php
if (defined('IN_ACP3') === false)
	exit;

$users = ACP3_CMS::$db2->fetchAll('SELECT id, nickname, realname, mail, website FROM ' . DB_PRE . 'users ORDER BY nickname ASC, id ASC LIMIT ' . POS . ',' . ACP3_CMS::$auth->entries);
$c_users = count($users);
$all_users = ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'users');

if ($c_users > 0) {
	ACP3_CMS::$view->assign('pagination', pagination($all_users));

	for ($i = 0; $i < $c_users; ++$i) {
		$pos = strrpos($users[$i]['realname'], ':');
		$users[$i]['realname_display'] = substr($users[$i]['realname'], $pos + 1);
		$users[$i]['realname'] = substr($users[$i]['realname'], 0, $pos);
		$pos = strrpos($users[$i]['mail'], ':');
		$users[$i]['mail_display'] = substr($users[$i]['mail'], $pos + 1);
		$users[$i]['mail'] = substr($users[$i]['mail'], 0, $pos);
		$pos = strrpos($users[$i]['website'], ':');
		$users[$i]['website_display'] = substr($users[$i]['website'], $pos + 1);
		$users[$i]['website'] = substr($users[$i]['website'], 0, $pos);
		if (!empty($users[$i]['website']) && (bool) preg_match('=^http(s)?://=', $users[$i]['website']) === false)
			$users[$i]['website'] =  'http://' . $users[$i]['website'];
	}
	ACP3_CMS::$view->assign('users', $users);
}
ACP3_CMS::$view->assign('LANG_users_found', sprintf(ACP3_CMS::$lang->t('users', 'users_found'), $all_users));

ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('users/list.tpl'));
