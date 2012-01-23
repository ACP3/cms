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

breadcrumb::assign($lang->t('common', 'acp'), $uri->route('acp'));
breadcrumb::assign($lang->t('system', 'system'), $uri->route('acp/system'));
breadcrumb::assign($lang->t('system', 'extensions'), $uri->route('acp/system/extensions'));
breadcrumb::assign($lang->t('system', 'modules'));

if ($uri->action == 'activate') {
	$info = modules::parseInfo($uri->dir);
	if ($info['protected']) {
		$text = $lang->t('system', 'mod_deactivate_forbidden');
	} else {
		$bool = $db->update('modules', array('active' => 1), 'name = \'' . $uri->dir . '\'');
		modules::setModulesCache();
		acl::setResourcesCache();

		$text = $bool ? $lang->t('system', 'mod_activate_success') : $lang->t('system', 'mod_activate_error');
	}
	view::setContent(comboBox($text, $uri->route('acp/system/modules')));
} elseif ($uri->action == 'deactivate') {
	$info = modules::parseInfo($uri->dir);
	if ($info['protected']) {
		$text = $lang->t('system', 'mod_deactivate_forbidden');
	} else {
		$bool = $db->update('modules', array('active' => 0), 'name = \'' . $uri->dir . '\'');
		modules::setModulesCache();
		acl::setResourcesCache();

		$text = $bool ? $lang->t('system', 'mod_deactivate_success') : $lang->t('system', 'mod_deactivate_error');
	}
	view::setContent(comboBox($text, $uri->route('acp/system/modules')));
} else {
	// Languagecache neu erstellen
	$lang->setLangCache();

	$mod_list = modules::modulesList();

	$tpl->assign('LANG_modules_found', sprintf($lang->t('system', 'modules_found'), count($mod_list)));
	$tpl->assign('modules', $mod_list);

	view::setContent(view::fetchTemplate('system/modules.tpl'));
}
