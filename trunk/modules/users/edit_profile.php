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

if (!$auth->is_user() || !preg_match('/\d/', $_SESSION['acp3_id'])) {
	redirect('errors/403');
} else {
	$breadcrumb->assign(lang('users', 'users'), uri('users'));
	$breadcrumb->assign(lang('users', 'home'), uri('users/home'));
	$breadcrumb->assign(lang('users', 'edit_profile'));

	if (isset($_POST['submit'])) {
		include 'modules/users/entry.php';
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$user = $db->select('name, mail', 'users', 'id = \'' . $_SESSION['acp3_id'] . '\'');

		$tpl->assign('form', isset($form) ? $form : $user[0]);

		$content = $tpl->fetch('users/edit_profile.html');
	}
}
?>