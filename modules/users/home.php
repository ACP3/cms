<?php
if (defined('IN_ACP3') === false)
	exit;

if (ACP3_CMS::$auth->isUser() === false || !ACP3_Validate::isNumber(ACP3_CMS::$auth->getUserId())) {
	ACP3_CMS::$uri->redirect('errors/403');
} else {
	ACP3_CMS::$breadcrumb->append(ACP3_CMS::$lang->t('users', 'users'), ACP3_CMS::$uri->route('users'));
	ACP3_CMS::$breadcrumb->append(ACP3_CMS::$lang->t('users', 'home'));

	if (isset($_POST['submit']) === true) {
		$bool = ACP3_CMS::$db->update('users', array('draft' => ACP3_CMS::$db->escape($_POST['draft'], 2)), 'id = \'' . ACP3_CMS::$auth->getUserId() . '\'');

		ACP3_CMS::setContent(confirmBox(ACP3_CMS::$lang->t('common', $bool !== false ? 'edit_success' : 'edit_error'), ACP3_CMS::$uri->route('users/home')));
	}
	if (isset($_POST['submit']) === false) {
		getRedirectMessage();

		$user = ACP3_CMS::$db->select('draft', 'users', 'id = \'' . ACP3_CMS::$auth->getUserId() . '\'');

		ACP3_CMS::$view->assign('draft', ACP3_CMS::$db->escape($user[0]['draft'], 3));

		ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('users/home.tpl'));
	}
}
