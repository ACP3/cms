<?php
if (defined('IN_ACP3') === false)
	exit;

if (ACP3_CMS::$auth->isUser() === false || !ACP3_Validate::isNumber(ACP3_CMS::$auth->getUserId())) {
	ACP3_CMS::$uri->redirect('errors/403');
} else {
	ACP3_CMS::$breadcrumb
	->append(ACP3_CMS::$lang->t('users', 'users'), ACP3_CMS::$uri->route('users'))
	->append(ACP3_CMS::$lang->t('users', 'home'));

	if (isset($_POST['submit']) === true) {
		$bool = ACP3_CMS::$db2->update(DB_PRE . 'users', array('draft' => $_POST['draft']), array('id' => ACP3_CMS::$auth->getUserId()));

		setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'users/home');
	}
	if (isset($_POST['submit']) === false) {
		getRedirectMessage();

		$user_draft = ACP3_CMS::$db2->fetchColumn('SELECT draft FROM ' . DB_PRE . 'users WHERE id = ?', array(ACP3_CMS::$auth->getUserId()));

		ACP3_CMS::$view->assign('draft', $user_draft);

		ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('users/home.tpl'));
	}
}
