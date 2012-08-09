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
		   ->append($lang->t('system', 'acp_languages'));

if ($uri->dir) {
	$dir = $lang->languagePackExists($uri->dir) ? $uri->dir : 0;
	$bool = false;

	if (!empty($dir)) {
		$bool = ACP3_Config::system(array('lang' => $dir));
	}
	$text = $bool === true ? $lang->t('system', 'languages_edit_success') : $lang->t('system', 'languages_edit_error');

	ACP3_View::setContent(confirmBox($text, $uri->route('acp/system/languages')));
} else {
	$languages = array();
	$directories = scandir(ACP3_ROOT . 'languages');
	$count_dir = count($directories);
	for ($i = 0; $i < $count_dir; ++$i) {
		$lang_info = ACP3_XML::parseXmlFile(ACP3_ROOT . 'languages/' . $directories[$i] . '/info.xml', '/language');
		if (!empty($lang_info)) {
			$languages[$i] = $lang_info;
			$languages[$i]['selected'] = CONFIG_LANG == $directories[$i] ? 1 : 0;
			$languages[$i]['dir'] = $directories[$i];
		}
	}
	$tpl->assign('languages', $languages);

	ACP3_View::setContent(ACP3_View::fetchTemplate('system/acp_languages.tpl'));
}
