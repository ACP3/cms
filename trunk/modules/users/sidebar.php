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

$currentPage = base64_encode((defined('IN_ADM') === true ? 'acp/' : '') . $uri->query);

if ($auth->isUser() === true) {
	$user_sidebar = array();
	$user_sidebar['page'] = $currentPage;

	// Module holen
	$mod_list = ACP3_Modules::getActiveModules();
	$nav_mods = $nav_system = array();
	$access_system = false;

	foreach ($mod_list as $name => $info) {
		$dir = $info['dir'];
		if (ACP3_Modules::check($dir, 'acp_list') === true && $dir !== 'acp') {
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
		if (ACP3_Modules::check('system', 'acp_configuration') === true) {
			$nav_system[$i]['page'] = 'configuration';
			$nav_system[$i]['name'] = $lang->t('system', 'acp_configuration');
		}
		if (ACP3_Modules::check('system', 'acp_extensions') === true) {
			$i++;
			$nav_system[$i]['page'] = 'extensions';
			$nav_system[$i]['name'] = $lang->t('system', 'acp_extensions');
		}
		if (ACP3_Modules::check('system', 'acp_maintenance') === true) {
			$i++;
			$nav_system[$i]['page'] = 'maintenance';
			$nav_system[$i]['name'] = $lang->t('system', 'acp_maintenance');
		}
		$user_sidebar['system'] = $nav_system;
	}

	$tpl->assign('user_sidebar', $user_sidebar);

	ACP3_View::displayTemplate('users/sidebar_user_menu.tpl');
} else {
	$settings = ACP3_Config::getModuleSettings('users');

	$tpl->assign('enable_registration', $settings['enable_registration']);
	$tpl->assign('uri', $uri->route(defined('IN_ADM') === true ? 'acp/users/login' : 'users/login'));
	$tpl->assign('redirect_uri', isset($_POST['redirect_uri']) ? $_POST['redirect_uri'] : $currentPage);

	ACP3_View::displayTemplate('users/sidebar_login.tpl');
}
