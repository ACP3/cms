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

$breadcrumb->assign($lang->t('system', 'extensions'), $uri->route('acp/system/extensions'))
		   ->assign($lang->t('system', 'languages'));

if ($uri->dir) {
	$dir = $lang->languagePackExists($uri->dir) ? $uri->dir : 0;
	$bool = false;

	if (!empty($dir)) {
		$bool = config::system(array('lang' => $dir));
	}
	$text = $bool === true ? $lang->t('system', 'languages_edit_success') : $lang->t('system', 'languages_edit_error');

	view::setContent(confirmBox($text, $uri->route('acp/system/languages')));
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

	view::setContent(view::fetchTemplate('system/languages.tpl'));
}
