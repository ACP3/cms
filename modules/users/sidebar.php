<?php
/**
 * Users
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

$currentPage = base64_encode(substr(str_replace(PHP_SELF, '', htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES)), 1));

if ($auth->isUser() === true) {
	$user_sidebar = array();
	$user_sidebar['page'] = $currentPage;

	// Module holen
	$mod_list = modules::modulesList();
	$nav_mods = array();
	$access_system = false;

	foreach ($mod_list as $name => $info) {
		$dir = $info['dir'];
		if (modules::check($dir, 'adm_list') === true && $dir !== 'acp') {
			if ($dir === 'system') {
				$access_system = true;
			} elseif ($dir === 'home') {
				$tpl->assign('access_home', true);
			} else {
				$nav_mods[$name]['name'] = $name;
				$nav_mods[$name]['dir'] = $dir;
			}
		}
	}
	if (!empty($nav_mods)) {
		$user_sidebar['modules'] = $nav_mods;
	}

	if ($access_system) {
		$i = 0;
		if (modules::check('system', 'configuration') === true) {
			$nav_system[$i]['page'] = 'configuration';
			$nav_system[$i]['name'] = $lang->t('system', 'configuration');
		}
		if (modules::check('system', 'extensions') === true) {
			$i++;
			$nav_system[$i]['page'] = 'extensions';
			$nav_system[$i]['name'] = $lang->t('system', 'extensions');
		}
		if (modules::check('system', 'maintenance') === true) {
			$i++;
			$nav_system[$i]['page'] = 'maintenance';
			$nav_system[$i]['name'] = $lang->t('system', 'maintenance');
		}
		$user_sidebar['system'] = $nav_system;
	}

	$tpl->assign('user_sidebar', $user_sidebar);

	view::displayTemplate('users/sidebar_user_menu.tpl');
} else {
	$settings = config::getModuleSettings('users');

	$tpl->assign('enable_registration', $settings['enable_registration']);
	$tpl->assign('uri', $uri->route(defined('IN_ADM') === true ? 'acp/users/login' : 'users/login'));
	$tpl->assign('redirect_uri', isset($_POST['form']['redirect_uri']) ? $_POST['form']['redirect_uri'] : $currentPage);

	view::displayTemplate('users/sidebar_login.tpl');
}
