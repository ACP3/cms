<?php
if (!defined('IN_ACP3'))
	exit;

if (!$auth->is_user() || !preg_match('/\d/', $_SESSION['acp3_id'])) {
	redirect('errors/403');
} else {
	$breadcrumb->assign(lang('users', 'users'), uri('users'));
	$breadcrumb->assign(lang('users', 'home'));

	if (isset($_POST['submit'])) {
		if (!$auth->is_user() || !preg_match('/\d/', $_SESSION['acp3_id'])) {
			redirect('errors/403');
		} else {
			$form = $_POST['form'];

			$bool = $db->update('users', array('draft' => $db->escape($form['draft'], 2)), 'id = \'' . $_SESSION['acp3_id'] . '\'');

			$content = combo_box($bool ? lang('users', 'draft_success') : lang('users', 'draft_error'), uri('users/home'));
		}
	}
	if (!isset($_POST['submit'])) {
		$user = $db->select('draft', 'users', 'id = \'' . $_SESSION['acp3_id'] . '\'');

		$tpl->assign('draft', $db->escape($user[0]['draft'], 3));

		$content = $tpl->fetch('users/home.html');
	}
}
?>