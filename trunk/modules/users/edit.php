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

if (!empty($modules->id) && $db->select('id', 'users', 'id = \'' . $modules->id . '\'', 0, 0, 0, 1) == '1') {
	if (isset($_POST['submit'])) {
		include 'modules/users/entry.php';
	}
	if (!isset($_POST['submit']) || isset($error_msg)) {
		$tpl->assign('error_msg', isset($error_msg) ? $error_msg : '');

		$user = $db->select('name, mail, access', 'users', 'id = \'' . $modules->id . '\'');

		$access = $db->select('id, name', 'access', 0, 'name ASC');
		$c_access = count($access);

		for ($i = 0; $i < $c_access; $i++) {
			$access[$i]['name'] = $access[$i]['name'];
			$access[$i]['selected'] = select_entry('access', $access[$i]['id'], $user[0]['access']);
		}
		$tpl->assign('access', $access);

		$tpl->assign('form', isset($form) ? $form : $user[0]);

		$content = $tpl->fetch('users/edit.html');
	}
} else {
	redirect('errors/404');
}
?>