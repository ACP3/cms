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
->append(ACP3_CMS::$lang->t('system', 'acp_designs'));

if (isset(ACP3_CMS::$uri->dir)) {
	$bool = false;

	if (is_file(ACP3_ROOT . 'designs/' . ACP3_CMS::$uri->dir . '/info.xml') === true) {
		$bool = ACP3_Config::setSettings('system', array('design' => ACP3_CMS::$uri->dir));

		// Cache leeren und diverse Parameter für die Template Engine abändern
		ACP3_Cache::purge('tpl_compiled');
		ACP3_Cache::purge('tpl_cached');
	}
	$text = ACP3_CMS::$lang->t('system', $bool === true ? 'designs_edit_success' : 'designs_edit_error');

	setRedirectMessage($bool, $text, 'acp/system/designs');
} else {
	getRedirectMessage();

	$designs = array();
	$directories = scandir(ACP3_ROOT . 'designs');
	$count_dir = count($directories);
	for ($i = 0; $i < $count_dir; ++$i) {
		$design_info = ACP3_XML::parseXmlFile(ACP3_ROOT . 'designs/' . $directories[$i] . '/info.xml', '/design');
		if (!empty($design_info)) {
			$designs[$i] = $design_info;
			$designs[$i]['selected'] = CONFIG_DESIGN == $directories[$i] ? 1 : 0;
			$designs[$i]['dir'] = $directories[$i];
		}
	}
	ACP3_CMS::$view->assign('designs', $designs);

	ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('system/acp_designs.tpl'));
}
