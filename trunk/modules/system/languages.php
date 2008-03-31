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

$breadcrumb->assign(lang('common', 'acp'), uri('acp'));
$breadcrumb->assign(lang('system', 'system'), uri('acp/system'));
$breadcrumb->assign(lang('system', 'extensions'), uri('acp/system/extensions'));
$breadcrumb->assign(lang('system', 'languages'));

if ($modules->dir) {
	$dir = is_file(ACP3_ROOT . 'languages/' . $modules->dir . '/info.php') ? $modules->dir : 0;
	$bool = false;

	if (!empty($dir)) {
		$bool = $config->general(array('lang' => $dir));
	}
	$text = $bool ? lang('system', 'languages_edit_success') : lang('system', 'languages_edit_error');

	$content = comboBox($text, uri('acp/system/languages'));
} else {
	$languages = array();
	$directories = scandir(ACP3_ROOT . 'languages');
	$count_dir = $validate->countArrayElements($directories);
	for ($i = 0; $i < $count_dir; $i++) {
		$lang_info = array();
		if ($directories[$i] != '.' && $directories[$i] != '..' && file_exists(ACP3_ROOT . 'languages/' . $directories[$i] . '/info.php')) {
			include ACP3_ROOT . 'languages/' . $directories[$i] . '/info.php';
			$languages[$i]['name'] = $lang_info['name'];
			$languages[$i]['description'] = $lang_info['description'];
			$languages[$i]['author'] = $lang_info['author'];
			$languages[$i]['version'] = $lang_info['version'];
			$languages[$i]['action'] = CONFIG_LANG == $directories[$i] ? '<img src="' . ROOT_DIR . 'images/crystal/16/active.png" alt="" />' : '<a href="' . uri('acp/system/languages/dir_' . $directories[$i]) . '"><img src="' . ROOT_DIR . 'images/crystal/16/inactive.png" alt="" /></a>';
		}
	}
	$tpl->assign('languages', $languages);

	$content = $tpl->fetch('system/languages.html');
}
?>