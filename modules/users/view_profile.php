<?php
/**
 * Users
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

ACP3_CMS::$breadcrumb
->append(ACP3_CMS::$lang->t('users', 'users'), ACP3_CMS::$uri->route('users'))
->append(ACP3_CMS::$lang->t('users', 'view_profile'));

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true &&
	ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'users WHERE id = ?', array(ACP3_CMS::$uri->id)) == 1) {
	$user = ACP3_CMS::$auth->getUserInfo(ACP3_CMS::$uri->id);
	$user['gender'] = str_replace(array(1, 2, 3), array('-', ACP3_CMS::$lang->t('users', 'female'), ACP3_CMS::$lang->t('users', 'male')), $user['gender']);
	$user['birthday'] = ACP3_CMS::$date->format($user['birthday'], $user['birthday_format'] == 1 ? 'd.m.Y' : 'd.m');
	if (!empty($user['website']) && (bool) preg_match('=^http(s)?://=', $user['website']) === false)
		$user['website'] =  'http://' . $user['website'];

	ACP3_CMS::$view->assign('user', $user);

	ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('users/view_profile.tpl'));	
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}