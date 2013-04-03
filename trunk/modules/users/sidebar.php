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

$currentPage = base64_encode((defined('IN_ADM') === true ? 'acp/' : '') . ACP3_CMS::$uri->query);

// Usermenü anzeigen, falls der Benutzer eingeloggt ist
if (ACP3_CMS::$auth->isUser() === true) {
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
				ACP3_CMS::$view->assign('access_home', true);
			} else {
				$nav_mods[$name]['name'] = $name;
				$nav_mods[$name]['dir'] = $dir;
				$nav_mods[$name]['active'] = defined('IN_ADM') === true && $dir === ACP3_CMS::$uri->mod ? ' class="active"' : '';
			}
		}
	}
	if (!empty($nav_mods)) {
		$user_sidebar['modules'] = $nav_mods;
	}

	if ($access_system === true) {
		$i = 0;
		if (ACP3_Modules::check('system', 'acp_configuration') === true) {
			$nav_system[$i]['page'] = 'configuration';
			$nav_system[$i]['name'] = ACP3_CMS::$lang->t('system', 'acp_configuration');
			$nav_system[$i]['active'] = ACP3_CMS::$uri->query === 'system/configuration/' ? ' class="active"' : '';
		}
		if (ACP3_Modules::check('system', 'acp_extensions') === true) {
			$i++;
			$nav_system[$i]['page'] = 'extensions';
			$nav_system[$i]['name'] = ACP3_CMS::$lang->t('system', 'acp_extensions');
			$nav_system[$i]['active'] = ACP3_CMS::$uri->query === 'system/extensions/' ? ' class="active"' : '';
		}
		if (ACP3_Modules::check('system', 'acp_maintenance') === true) {
			$i++;
			$nav_system[$i]['page'] = 'maintenance';
			$nav_system[$i]['name'] = ACP3_CMS::$lang->t('system', 'acp_maintenance');
			$nav_system[$i]['active'] = ACP3_CMS::$uri->query === 'system/maintenance/' ? ' class="active"' : '';
		}
		$user_sidebar['system'] = $nav_system;
	}

	ACP3_CMS::$view->assign('user_sidebar', $user_sidebar);

	ACP3_CMS::$view->displayTemplate('users/sidebar_user_menu.tpl');
} else {
	$settings = ACP3_Config::getSettings('users');

	ACP3_CMS::$view->assign('enable_registration', $settings['enable_registration']);
	ACP3_CMS::$view->assign('redirect_uri', isset($_POST['redirect_uri']) ? $_POST['redirect_uri'] : $currentPage);

	ACP3_CMS::$view->displayTemplate('users/sidebar_login.tpl');
}