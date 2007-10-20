<?php
/**
 * Users
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;

if (isset($_POST['entries']) || isset($modules->gen['entries'])) {
	include 'modules/users/entry.php';
} else {
	$users = $db->select('u.id, u.nickname, u.mail, a.name AS access', 'users AS u, ' . CONFIG_DB_PRE . 'access AS a', 'u.access = a.id', 'u.nickname ASC', POS, CONFIG_ENTRIES);
	$c_users = count($users);

	if ($c_users > 0) {
		$tpl->assign('pagination', pagination($db->select('id', 'users', 0, 0, 0, 0, 1)));

		$tpl->assign('users', $users);
	}

	$content = $tpl->fetch('users/acp_list.html');
}
?>