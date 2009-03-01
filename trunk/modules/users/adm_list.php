<?php
/**
 * Users
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

$users = $db->select('u.id, u.nickname, u.mail, a.name AS access', 'users AS u, ' . CONFIG_DB_PRE . 'access AS a', 'u.access = a.id', 'u.nickname ASC', POS, CONFIG_ENTRIES);
$c_users = count($users);

if ($c_users > 0) {
	$tpl->assign('pagination', pagination($db->countRows('*', 'users')));

	$tpl->assign('users', $users);
}

$content = $tpl->fetch('users/adm_list.html');
?>