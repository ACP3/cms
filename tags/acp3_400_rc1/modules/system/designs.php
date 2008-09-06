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
		$config = array(
			'date' => CONFIG_DATE,
			'db_host' => CONFIG_DB_HOST,
			'db_name' => CONFIG_DB_NAME,
			'db_pre' => CONFIG_DB_PRE,
			'db_pwd' => CONFIG_DB_PWD,
			'db_type' => CONFIG_DB_TYPE,
			'db_user' => CONFIG_DB_USER,
			'design' => $dir,
			'dst' => CONFIG_DST,
			'entries' => CONFIG_ENTRIES,
			'flood' => CONFIG_FLOOD,
			'homepage' => CONFIG_HOMEPAGE,
			'lang' => CONFIG_LANG,
			'maintenance' => CONFIG_MAINTENANCE,
			'maintenance_msg' => CONFIG_MAINTENANCE_MSG,
			'meta_description' => CONFIG_META_DESCRIPTION,
			'meta_keywords' => CONFIG_META_KEYWORDS,
			'sef' => CONFIG_SEF,
			'time_zone' => CONFIG_TIME_ZONE,
			'title' => CONFIG_TITLE,
			'version' => CONFIG_VERSION,
			'wysiwyg' => CONFIG_WYSIWYG
		);
		$bool = config::system($config);
	}
	$text = $bool ? $lang->t('system', 'designs_edit_success') : $lang->t('system', 'designs_edit_error');

	// Cache leeren und diverse Parameter für die Template Engine abändern
	cache::purge();
	$tpl->template_dir = ACP3_ROOT . 'designs/' . $dir . '/';
	$tpl->assign('design_path', ROOT_DIR . 'designs/' . $dir . '/');

	$content = comboBox($text, uri('acp/system/designs'));
} else {
	$designs = array();
	$directories = scandir(ACP3_ROOT . 'designs');
	$count_dir = count($directories);
	for ($i = 0; $i < $count_dir; ++$i) {
		$design_info = xml::parseXmlFile(ACP3_ROOT . 'designs/' . $directories[$i] . '/info.xml', '/design');
		if (!empty($design_info)) {
			$designs[$i] = $design_info;
			$designs[$i]['action'] = CONFIG_DESIGN == $directories[$i] ? '<img src="' . ROOT_DIR . 'images/crystal/16/apply.png" alt="" />' : '<a href="' . uri('acp/system/designs/dir_' . urlencode($directories[$i])) . '"><img src="' . ROOT_DIR . 'images/crystal/16/cancel.png" alt="" /></a>';
		}
	}
	$tpl->assign('designs', $designs);

	$content = $tpl->fetch('system/designs.html');
}
?>