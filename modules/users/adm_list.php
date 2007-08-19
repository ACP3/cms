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

if (isset($_POST['entries']) || isset($modules->gen['entries'])) {
	include 'modules/users/entry.php';
} else {
	$users = $db->select('id, name, access, mail', 'users', 0, 'name ASC', POS, CONFIG_ENTRIES);
	$c_users = count($users);

	if ($c_users > 0) {
		$tpl->assign('pagination', pagination($db->select('id', 'users', 0, 0, 0, 0, 1)));

		for ($i = 0; $i < $c_users; $i++) {
			$users[$i]['name'] = $users[$i]['name'];
			$access_name = $db->select('name', 'access', 'id = \'' . $users[$i]['access'] . '\'');
			$users[$i]['access'] = $access_name[0]['name'];
		}
		$tpl->assign('users', $users);
	}

	$content = $tpl->fetch('users/adm_list.html');
}
?>