<?php
/**
 * System
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

$breadcrumb->append($lang->t('system', 'acp_extensions'), $uri->route('acp/system/extensions'))
		   ->append($lang->t('system', 'acp_modules'));

switch ($uri->action) {
	case 'activate':
	case 'deactivate':
		$info = ACP3_Modules::parseInfo($uri->dir);
		if (empty($info)) {
			$text = $lang->t('system', 'module_not_found');
		} elseif ($info['protected']) {
			$text = $lang->t('system', 'mod_deactivate_forbidden');
		} else {
			$bool = $db->update('modules', array('active' => $uri->action === 'activate' ? 1 : 0), 'name = \'' . $db->escape($uri->dir) . '\'');
			ACP3_Modules::setModulesCache();
			ACP3_ACL::setResourcesCache();

			$text = $lang->t('system', 'mod_' . $uri->action . '_' . ($bool !== false ? 'success' : 'error'));
		}
		ACP3_View::setContent(confirmBox($text, $uri->route('acp/system/modules')));
		break;
	case 'install':
		// Nur noch nicht installierte Module berücksichtigen
		if ($db->countRows('*', 'modules', 'name = \'' . $db->escape($uri->dir) . '\'') == 0) {
			$bool = false;
			$path = MODULES_DIR . $uri->dir . '/install.class.php';
			if (is_file($path) === true) {
				require $path;

				$className = 'ACP3_' . preg_replace('/(\s+)/', '', ucwords(strtolower(str_replace('_', ' ', $uri->dir)))) . 'ModuleInstaller';
				$install = new $className();
				$bool = $install->install();
			}

			$text = $lang->t('system', $bool !== false ? 'mod_installation_success' : 'mod_installation_error');
		} else {
			$text = $lang->t('system', 'module_already_installed');
		}
		ACP3_View::setContent(confirmBox($text, $uri->route('acp/system/modules')));
		break;
	case 'uninstall':
		$mod_info = ACP3_Modules::parseInfo($uri->dir);
		// Nur installierte und Nicht-Core-Module berücksichtigen
		if ($db->countRows('*', 'modules', 'name = \'' . $db->escape($uri->dir) . '\'') == 1 && $mod_info['protected'] === false) {
			$bool = false;
			$path = MODULES_DIR . $uri->dir . '/install.class.php';
			if (is_file($path) === true) {
				require $path;

				$mod_id = $db->select('id', 'modules', 'name = \'' . $db->escape($uri->dir) . '\'');
				$className = 'ACP3_' . preg_replace('/(\s+)/', '', ucwords(strtolower(str_replace('_', ' ', $uri->dir)))) . 'ModuleInstaller';
				$install = new $className();
				$install->setModuleId($mod_id[0]['id']);
				$bool = $install->uninstall();
			}

			$text = $lang->t('system', $bool !== false ? 'mod_uninstallation_success' : 'mod_uninstallation_error');
		} else {
			$text = $lang->t('system', 'module_not_uninstallable');
		}
		ACP3_View::setContent(confirmBox($text, $uri->route('acp/system/modules')));
		break;
	default:
		// Languagecache neu erstellen, für den Fall, dass neue Module hinzugefügt wurden
		$lang->setLangCache();

		$modules = ACP3_Modules::getAllModules();
		$installed_modules = $new_modules = array();

		foreach ($modules as $key => $values) {
			if ($db->countRows('*', 'modules', 'name = \'' . $values['dir'] . '\'') == 1) {
				$installed_modules[$key] = $values;
			} else {
				$new_modules[$key] = $values;
			}
		}

		$tpl->assign('installed_modules', $installed_modules);
		$tpl->assign('new_modules', $new_modules);

		ACP3_View::setContent(ACP3_View::fetchTemplate('system/acp_modules.tpl'));
}