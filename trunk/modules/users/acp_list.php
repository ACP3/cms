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

$users = ACP3_CMS::$db->select('u.id, u.nickname, u.mail', 'users AS u', 0, 'u.nickname ASC', POS, ACP3_CMS::$auth->entries);
$c_users = count($users);

if ($c_users > 0) {
	ACP3_CMS::$view->assign('pagination', pagination(ACP3_CMS::$db->countRows('*', 'users')));

	for ($i = 0; $i < $c_users; ++$i) {
		$users[$i]['nickname'] = ACP3_CMS::$db->escape($users[$i]['nickname'], 3);
		$users[$i]['roles'] = implode(', ', ACP3_ACL::getUserRoles($users[$i]['id'], 2));
		$users[$i]['mail'] = substr($users[$i]['mail'], 0, -2);
	}
	ACP3_CMS::$view->assign('users', $users);
	ACP3_CMS::$view->assign('can_delete', ACP3_Modules::check('users', 'acp_delete'));
}

ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('users/acp_list.tpl'));
