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

if ($uri->action === 'activate') {
	$info = ACP3_Modules::parseInfo($uri->dir);
	if ($info['protected']) {
		$text = $lang->t('system', 'mod_deactivate_forbidden');
	} else {
		$bool = $db->update('modules', array('active' => 1), 'name = \'' . $uri->dir . '\'');
		ACP3_Modules::setModulesCache();
		ACP3_ACL::setResourcesCache();

		$text = $bool !== false ? $lang->t('system', 'mod_activate_success') : $lang->t('system', 'mod_activate_error');
	}
	ACP3_View::setContent(confirmBox($text, $uri->route('acp/system/modules')));
} elseif ($uri->action === 'deactivate') {
	$info = ACP3_Modules::parseInfo($uri->dir);
	if ($info['protected']) {
		$text = $lang->t('system', 'mod_deactivate_forbidden');
	} else {
		$bool = $db->update('modules', array('active' => 0), 'name = \'' . $uri->dir . '\'');
		ACP3_Modules::setModulesCache();
		ACP3_ACL::setResourcesCache();

		$text = $bool !== false ? $lang->t('system', 'mod_deactivate_success') : $lang->t('system', 'mod_deactivate_error');
	}
	ACP3_View::setContent(confirmBox($text, $uri->route('acp/system/modules')));
} else {
	// Languagecache neu erstellen
	$lang->setLangCache();

	$mod_list = ACP3_Modules::getAllModules();

	$tpl->assign('LANG_modules_found', sprintf($lang->t('system', 'modules_found'), count($mod_list)));
	$tpl->assign('modules', $mod_list);

	ACP3_View::setContent(ACP3_View::fetchTemplate('system/modules.tpl'));
}
