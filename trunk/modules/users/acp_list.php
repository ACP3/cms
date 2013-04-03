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

$users = ACP3_CMS::$db2->fetchAll('SELECT id, nickname, mail FROM ' . DB_PRE . 'users ORDER BY nickname ASC');
$c_users = count($users);

if ($c_users > 0) {
	$can_delete = ACP3_Modules::check('users', 'acp_delete');
	$config = array(
		'element' => '#acp-table',
		'sort_col' => $can_delete === true ? 1 : 0,
		'sort_dir' => 'asc',
		'hide_col_sort' => $can_delete === true ? 0 : ''
	);
	ACP3_CMS::setContent(datatable($config));

	for ($i = 0; $i < $c_users; ++$i) {
		$users[$i]['roles'] = implode(', ', ACP3_ACL::getUserRoles($users[$i]['id'], 2));
	}
	ACP3_CMS::$view->assign('users', $users);
	ACP3_CMS::$view->assign('can_delete', $can_delete);
}

ACP3_CMS::appendContent(ACP3_CMS::$view->fetchTemplate('users/acp_list.tpl'));