<?php
/**
 * System
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

$breadcrumb->append($lang->t('system', 'extensions'), $uri->route('acp/system/extensions'))
		   ->append($lang->t('system', 'modules'));

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

			$text = $bool !== false ? $lang->t('system', $uri->action === 'activate' ? 'mod_activate_success' : 'mod_deactivate_success') : $lang->t('system', $uri->action === 'activate' ? 'mod_activate_error' : 'mod_deactivate_error');
		}
		ACP3_View::setContent(confirmBox($text, $uri->route('acp/system/modules')));
		break;
	case 'install':
		// Nur noch nicht installierte Module berücksichtigen
		if ($db->countRows('*', 'modules', 'name = \'' . $db->escape($uri->dir) . '\'') == 0) {
			
		} else {
			$text = $lang->t('system', 'module_already_installed');
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
				$new_mods[$key] = $values;
			}
		}

		$tpl->assign('installed_modules', $installed_modules);
		$tpl->assign('new_modules', $new_modules);

		ACP3_View::setContent(ACP3_View::fetchTemplate('system/modules.tpl'));
}