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
breadcrumb::assign($lang->t('system', 'designs'));

if ($uri->dir) {
	$dir = is_file(ACP3_ROOT . 'designs/' . $uri->dir . '/info.xml') ? $uri->dir : 0;
	$bool = false;

	if (!empty($dir)) {
		$bool = config::system(array('design' => $dir));

		// Cache leeren und diverse Parameter für die Template Engine abändern
		cache::purge();
		$tpl->setTemplateDir(ACP3_ROOT . 'designs/' . $dir . '/');
		$tpl->assign('DESIGN_PATH', ROOT_DIR . 'designs/' . $dir . '/');
	}
	$text = $bool ? $lang->t('system', 'designs_edit_success') : $lang->t('system', 'designs_edit_error');

	$content = comboBox($text, uri('acp/system/designs'));
} else {
	$designs = array();
	$directories = scandir(ACP3_ROOT . 'designs');
	$count_dir = count($directories);
	for ($i = 0; $i < $count_dir; ++$i) {
		$design_info = xml::parseXmlFile(ACP3_ROOT . 'designs/' . $directories[$i] . '/info.xml', '/design');
		if (!empty($design_info)) {
			$designs[$i] = $design_info;
			$designs[$i]['selected'] = CONFIG_DESIGN == $directories[$i] ? 1 : 0;
			$designs[$i]['dir'] = $directories[$i];
		}
	}
	$tpl->assign('designs', $designs);

	$content = modules::fetchTemplate('system/designs.html');
}
