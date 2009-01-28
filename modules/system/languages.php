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
breadcrumb::assign($lang->t('system', 'languages'));

if ($uri->dir) {
	$dir = is_file(ACP3_ROOT . 'languages/' . $uri->dir . '/info.xml') ? $uri->dir : 0;
	$bool = false;

	if (!empty($dir)) {
		$config = array(
			'date' => CONFIG_DATE_FORMAT,
			'db_host' => CONFIG_DB_HOST,
			'db_name' => CONFIG_DB_NAME,
			'db_pre' => CONFIG_DB_PRE,
			'db_pwd' => CONFIG_DB_PASSWORD,
			'db_type' => CONFIG_DB_TYPE,
			'db_user' => CONFIG_DB_USER,
			'design' => CONFIG_DESIGN,
			'dst' => CONFIG_DATE_DST,
			'entries' => CONFIG_ENTRIES,
			'flood' => CONFIG_FLOOD,
			'homepage' => CONFIG_HOMEPAGE,
			'lang' => $dir,
			'maintenance' => CONFIG_MAINTENANCE_MODE,
			'maintenance_msg' => CONFIG_MAINTENANCE_MESSAGE,
			'meta_description' => CONFIG_SEO_META_DESCRIPTION,
			'meta_keywords' => CONFIG_SEO_META_KEYWORDS,
			'sef' => CONFIG_SEO_MOD_REWRITE,
			'time_zone' => CONFIG_DATE_TIME_ZONE,
			'title' => CONFIG_SEO_TITLE,
			'version' => CONFIG_VERSION,
			'wysiwyg' => CONFIG_WYSIWYG
		);
		$bool = config::system($config);
	}
	$text = $bool ? $lang->t('system', 'languages_edit_success') : $lang->t('system', 'languages_edit_error');

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