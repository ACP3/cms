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
		   ->append(ACP3_CMS::$lang->t('system', 'acp_languages'));

if (ACP3_CMS::$uri->dir) {
	$dir = ACP3_CMS::$lang->languagePackExists(ACP3_CMS::$uri->dir) ? ACP3_CMS::$uri->dir : 0;
	$bool = false;

	if (!empty($dir)) {
		$bool = ACP3_Config::setSettings('system', array('lang' => $dir));
	}
	$text = $bool === true ? ACP3_CMS::$lang->t('system', 'languages_edit_success') : ACP3_CMS::$lang->t('system', 'languages_edit_error');

	ACP3_CMS::setContent(confirmBox($text, ACP3_CMS::$uri->route('acp/system/languages')));
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
	ACP3_CMS::$view->assign('languages', $languages);

	ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('system/acp_languages.tpl'));
}
