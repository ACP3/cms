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
		   ->append($lang->t('system', 'acp_designs'));

if ($uri->dir) {
	$dir = is_file(ACP3_ROOT . 'designs/' . $uri->dir . '/info.xml') ? $uri->dir : 0;
	$bool = false;

	if (!empty($dir)) {
		$bool = ACP3_Config::setSettings('system', array('design' => $dir));

		// Cache leeren und diverse Parameter für die Template Engine abändern
		ACP3_Cache::purge();
		$tpl->setTemplateDir(ACP3_ROOT . 'designs/' . $dir . '/');
		$tpl->assign('DESIGN_PATH', ROOT_DIR . 'designs/' . $dir . '/');
	}
	$text = $bool === true ? $lang->t('system', 'designs_edit_success') : $lang->t('system', 'designs_edit_error');

	ACP3_View::setContent(confirmBox($text, $uri->route('acp/system/designs')));
} else {
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
	$tpl->assign('designs', $designs);

	ACP3_View::setContent(ACP3_View::fetchTemplate('system/acp_designs.tpl'));
}
