<?php
/**
 * System
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

breadcrumb::assign($lang->t('common', 'acp'), uri('acp'));
breadcrumb::assign($lang->t('system', 'system'), uri('acp/system'));
breadcrumb::assign($lang->t('system', 'extensions'), uri('acp/system/extensions'));
breadcrumb::assign($lang->t('system', 'modules'));

if ($uri->action == 'activate') {
	$info = modules::parseInfo($uri->dir);
	if ($info['protected']) {
		$text = $lang->t('system', 'mod_deactivate_forbidden');
	} else {
		$bool = xml::writeToXml(ACP3_ROOT . 'modules/' . $uri->dir . '/module.xml', 'info/*', array('active' => '1'));

		$text = $bool ? $lang->t('system', 'mod_activate_success') : $lang->t('system', 'mod_activate_error');
	}
	$content = comboBox($text, uri('acp/system/modules'));
} elseif ($uri->action == 'deactivate') {
	$info = modules::parseInfo($uri->dir);
	if ($info['protected']) {
		$text = $lang->t('system', 'mod_deactivate_forbidden');
	} else {
		$bool = xml::writeToXml(ACP3_ROOT . 'modules/' . $uri->dir . '/module.xml', 'info/*', array('active' => '0'));

		$text = $bool ? $lang->t('system', 'mod_deactivate_success') : $lang->t('system', 'mod_deactivate_error');
	}
	$content = comboBox($text, uri('acp/system/modules'));
} else {
	// Languagecache neu erstellen
	$lang->setLangCache();

	$mod_list = modules::modulesList();

	foreach ($mod_list as $name => $info) {
		if ($info['protected']) {
			$mod_list[$name]['action'] = '<img src="' . ROOT_DIR . 'images/crystal/16/editdelete.png" alt="" />';
		} elseif ($info['active']) {
			$mod_list[$name]['action'] = '<a href="' . uri('acp/system/modules/action_deactivate/dir_' . $info['dir']) . '" title="' . $lang->t('system', 'disable_module') . '"><img src="' . ROOT_DIR . 'images/crystal/16/apply.png" alt="" /></a>';
		} else {
			$mod_list[$name]['action'] = '<a href="' . uri('acp/system/modules/action_activate/dir_' . $info['dir']) . '" title="' . $lang->t('system', 'enable_module') . '"><img src="' . ROOT_DIR . 'images/crystal/16/cancel.png" alt="" /></a>';
		}

	}

	$tpl->assign('LANG_modules_found', sprintf($lang->t('system', 'modules_found'), count($mod_list)));
	$tpl->assign('modules', $mod_list);

	$content = $tpl->fetch('system/modules.html');
}
