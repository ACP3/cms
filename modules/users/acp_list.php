<?php
/**
 * Users
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

getRedirectMessage();

$users = $db->select('u.id, u.nickname, u.mail', 'users AS u', 0, 'u.nickname ASC', POS, $auth->entries);
$c_users = count($users);

if ($c_users > 0) {
	$tpl->assign('pagination', pagination($db->countRows('*', 'users')));

	for ($i = 0; $i < $c_users; ++$i) {
		$users[$i]['nickname'] = $db->escape($users[$i]['nickname'], 3);
		$users[$i]['roles'] = implode(', ', ACP3_ACL::getUserRoles($users[$i]['id'], 2));
		$users[$i]['mail'] = substr($users[$i]['mail'], 0, -2);
	}
	$tpl->assign('users', $users);
	$tpl->assign('can_delete', ACP3_Modules::check('users', 'acp_delete'));
}

ACP3_View::setContent(ACP3_View::fetchTemplate('users/acp_list.tpl'));
