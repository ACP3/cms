<?php
if (defined('IN_ACP3') === false)
	exit;

if ($auth->isUser() === false || !validate::isNumber($auth->getUserId())) {
	$uri->redirect('errors/403');
} else {
	$breadcrumb->assign($lang->t('users', 'users'), $uri->route('users'));
	$breadcrumb->assign($lang->t('users', 'home'));

	if (isset($_POST['form']) === true) {
		$form = $_POST['form'];

		$bool = $db->update('users', array('draft' => $db->escape($form['draft'], 2)), 'id = \'' . $auth->getUserId() . '\'');

		view::setContent(confirmBox($bool !== false ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), $uri->route('users/home')));
	}
	if (isset($_POST['form']) === false) {
		getRedirectMessage();

		$user = $db->select('draft', 'users', 'id = \'' . $auth->getUserId() . '\'');

		$tpl->assign('draft', $db->escape($user[0]['draft'], 3));

		view::setContent(view::fetchTemplate('users/home.tpl'));
	}
}
