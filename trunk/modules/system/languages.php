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

breadcrumb::assign(lang('common', 'acp'), uri('acp'));
breadcrumb::assign(lang('system', 'system'), uri('acp/system'));
breadcrumb::assign(lang('system', 'extensions'), uri('acp/system/extensions'));
breadcrumb::assign(lang('system', 'languages'));

if ($modules->dir) {
	$dir = is_file(ACP3_ROOT . 'languages/' . $modules->dir . '/info.xml') ? $modules->dir : 0;
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
			'design' => CONFIG_DESIGN,
			'dst' => CONFIG_DST,
			'entries' => CONFIG_ENTRIES,
			'flood' => CONFIG_FLOOD,
			'homepage' => CONFIG_HOMEPAGE,
			'lang' => $dir,
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
	$text = $bool ? lang('system', 'languages_edit_success') : lang('system', 'languages_edit_error');

	$content = comboBox($text, uri('acp/system/languages'));
} else {
	$languages = array();
	$directories = scandir(ACP3_ROOT . 'languages');
	$count_dir = count($directories);
	for ($i = 0; $i < $count_dir; ++$i) {
		$lang_info = xml::parseXmlFile(ACP3_ROOT . 'languages/' . $directories[$i] . '/info.xml', '/language');
		if (!empty($lang_info)) {
			$languages[$i] = $lang_info;
			$languages[$i]['action'] = CONFIG_LANG == $directories[$i] ? '<img src="' . ROOT_DIR . 'images/crystal/16/apply.png" alt="" />' : '<a href="' . uri('acp/system/languages/dir_' . $directories[$i]) . '"><img src="' . ROOT_DIR . 'images/crystal/16/cancel.png" alt="" /></a>';
		}
	}
	$tpl->assign('languages', $languages);

	$content = $tpl->fetch('system/languages.html');
}
?>