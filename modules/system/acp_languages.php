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

ACP3_CMS::$breadcrumb
->append(ACP3_CMS::$lang->t('system', 'acp_extensions'), ACP3_CMS::$uri->route('acp/system/extensions'))
->append(ACP3_CMS::$lang->t('system', 'acp_languages'));

if (isset(ACP3_CMS::$uri->dir)) {
	$bool = false;

	if (ACP3_CMS::$lang->languagePackExists(ACP3_CMS::$uri->dir) === true) {
		$bool = ACP3_Config::setSettings('system', array('lang' => ACP3_CMS::$uri->dir));
		ACP3_CMS::$lang->setLanguage(ACP3_CMS::$uri->dir);
	}
	$text = ACP3_CMS::$lang->t('system', $bool === true ? 'languages_edit_success' : 'languages_edit_error');

	setRedirectMessage($bool, $text, 'acp/system/languages');
} else {
	getRedirectMessage();

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
}
