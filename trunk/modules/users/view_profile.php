<?php
if (defined('IN_ACP3') === false)
	exit;

$breadcrumb->append($lang->t('users', 'users'), $uri->route('users'))
		   ->append($lang->t('users', 'view_profile'));

if (ACP3_Validate::isNumber($uri->id) === true && $db->countRows('*', 'users', 'id = \'' . $uri->id . '\'') == 1) {
	$user = $auth->getUserInfo($uri->id);
	$user['gender'] = str_replace(array(1, 2, 3), array('-', $lang->t('users', 'female'), $lang->t('users', 'male')), $user['gender']);
	$user['birthday'] = $date->format($user['birthday'], $user['birthday_format'] == 1 ? 'd.m.Y' : 'd.m');
	$tpl->assign('user', $user);

	ACP3_View::setContent(ACP3_View::fetchTemplate('users/view_profile.tpl'));	
} else {
	$uri->redirect('errors/404');
}
