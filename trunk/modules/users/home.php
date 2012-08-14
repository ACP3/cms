<?php
if (defined('IN_ACP3') === false)
	exit;

if ($auth->isUser() === false || !ACP3_Validate::isNumber($auth->getUserId())) {
	$uri->redirect('errors/403');
} else {
	$breadcrumb->append($lang->t('users', 'users'), $uri->route('users'));
	$breadcrumb->append($lang->t('users', 'home'));

	if (isset($_POST['submit']) === true) {
		$bool = $db->update('users', array('draft' => $db->escape($_POST['draft'], 2)), 'id = \'' . $auth->getUserId() . '\'');

		ACP3_View::setContent(confirmBox($lang->t('common', $bool !== false ? 'edit_success' : 'edit_error'), $uri->route('users/home')));
	}
	if (isset($_POST['submit']) === false) {
		getRedirectMessage();

		$user = $db->select('draft', 'users', 'id = \'' . $auth->getUserId() . '\'');

		$tpl->assign('draft', $db->escape($user[0]['draft'], 3));

		ACP3_View::setContent(ACP3_View::fetchTemplate('users/home.tpl'));
	}
}
