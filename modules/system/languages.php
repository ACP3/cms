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
breadcrumb::assign($lang->t('system', 'languages'));

if ($uri->dir) {
	$dir = $lang->languagePackExists($uri->dir) ? $uri->dir : 0;
	$bool = false;

	if (!empty($dir)) {
		$bool = config::system(array('lang' => $dir));
	}
	$text = $bool ? $lang->t('system', 'languages_edit_success') : $lang->t('system', 'languages_edit_error');

	$content = comboBox($text, $uri->route('acp/system/languages'));
} else {
	$languages = array();
	$directories = scandir(ACP3_ROOT . 'languages');
	$count_dir = count($directories);
	for ($i = 0; $i < $count_dir; ++$i) {
		$lang_info = xml::parseXmlFile(ACP3_ROOT . 'languages/' . $directories[$i] . '/info.xml', '/language');
		if (!empty($lang_info)) {
			$languages[$i] = $lang_info;
			$languages[$i]['selected'] = CONFIG_LANG == $directories[$i] ? 1 : 0;
			$languages[$i]['dir'] = $directories[$i];
		}
	}
	$tpl->assign('languages', $languages);

	$content = modules::fetchTemplate('system/languages.html');
}
