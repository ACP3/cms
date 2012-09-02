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

ACP3_CMS::$breadcrumb->append(ACP3_CMS::$lang->t('system', 'acp_extensions'), ACP3_CMS::$uri->route('acp/system/extensions'))
		   ->append(ACP3_CMS::$lang->t('system', 'acp_modules'));

switch (ACP3_CMS::$uri->action) {
	case 'activate':
	case 'deactivate':
		$info = ACP3_Modules::getModuleInfo(ACP3_CMS::$uri->dir);
		if (empty($info)) {
			$text = ACP3_CMS::$lang->t('system', 'module_not_found');
		} elseif ($info['protected'] === true) {
			$text = ACP3_CMS::$lang->t('system', 'mod_deactivate_forbidden');
		} else {
			$bool = ACP3_CMS::$db->update('modules', array('active' => ACP3_CMS::$uri->action === 'activate' ? 1 : 0), 'name = \'' . ACP3_CMS::$db->escape(ACP3_CMS::$uri->dir) . '\'');
			ACP3_Modules::setModulesCache();
			ACP3_ACL::setResourcesCache();

			$text = ACP3_CMS::$lang->t('system', 'mod_' . ACP3_CMS::$uri->action . '_' . ($bool !== false ? 'success' : 'error'));
		}
		setRedirectMessage($bool, $text, 'acp/system/modules');
		break;
	case 'install':
		$bool = false;
		// Nur noch nicht installierte Module berücksichtigen
		if (ACP3_CMS::$db->countRows('*', 'modules', 'name = \'' . ACP3_CMS::$db->escape(ACP3_CMS::$uri->dir) . '\'') == 0) {
			$path = MODULES_DIR . ACP3_CMS::$uri->dir . '/install.class.php';
			if (is_file($path) === true) {
				require $path;

				$className = 'ACP3_' . preg_replace('/(\s+)/', '', ucwords(strtolower(str_replace('_', ' ', ACP3_CMS::$uri->dir)))) . 'ModuleInstaller';
				$install = new $className();
				$bool = $install->install();

				// Cache aktualisieren
				ACP3_Modules::setModulesCache();
			}

			$text = ACP3_CMS::$lang->t('system', 'mod_installation_' . ($bool !== false ? 'success' : 'error'));
		} else {
			$text = ACP3_CMS::$lang->t('system', 'module_already_installed');
		}
		setRedirectMessage($bool, $text, 'acp/system/modules');
		break;
	case 'uninstall':
		$bool = false;
		$mod_info = ACP3_Modules::getModuleInfo(ACP3_CMS::$uri->dir);
		// Nur installierte und Nicht-Core-Module berücksichtigen
		if (ACP3_CMS::$db->countRows('*', 'modules', 'name = \'' . ACP3_CMS::$db->escape(ACP3_CMS::$uri->dir) . '\'') == 1 && $mod_info['protected'] === false) {
			$path = MODULES_DIR . ACP3_CMS::$uri->dir . '/install.class.php';
			if (is_file($path) === true) {
				require $path;

				$className = 'ACP3_' . preg_replace('/(\s+)/', '', ucwords(strtolower(str_replace('_', ' ', ACP3_CMS::$uri->dir)))) . 'ModuleInstaller';
				$install = new $className();
				$bool = $install->uninstall();

				// Cache aktualisieren
				ACP3_Modules::setModulesCache();
			}

			$text = ACP3_CMS::$lang->t('system', 'mod_uninstallation_' . ($bool !== false ? 'success' : 'error'));
		} else {
			$text = ACP3_CMS::$lang->t('system', 'protected_module_description');
		}
		setRedirectMessage($bool, $text, 'acp/system/modules');
		break;
	default:
		getRedirectMessage();

		// Languagecache neu erstellen, für den Fall, dass neue Module hinzugefügt wurden
		ACP3_CMS::$lang->setLangCache();

		$modules = ACP3_Modules::getAllModules();
		$installed_modules = $new_modules = array();

		foreach ($modules as $key => $values) {
			if (ACP3_CMS::$db->countRows('*', 'modules', 'name = \'' . $values['dir'] . '\'') == 1) {
				$installed_modules[$key] = $values;
			} else {
				$new_modules[$key] = $values;
			}
		}

		ACP3_CMS::$view->assign('installed_modules', $installed_modules);
		ACP3_CMS::$view->assign('new_modules', $new_modules);

		ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('system/acp_modules.tpl'));
}