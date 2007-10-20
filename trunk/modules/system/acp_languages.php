<?php
/**
 * System
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;

$breadcrumb->assign(lang('system', 'system'), uri('system/acp_list'));
$breadcrumb->assign(lang('system', 'acp_extensions'), uri('system/acp_extensions'));
$breadcrumb->assign(lang('system', 'acp_languages'));

$languages = array();
$directories = scandir('languages');
$count_dir = count($directories);
for ($i = 0; $i < $count_dir; $i++) {
	$lang_info = array();
	if ($directories[$i] != '.' && $directories[$i] != '..' && file_exists('languages/' . $directories[$i] . '/info.php')) {
		include 'languages/' . $directories[$i] . '/info.php';
		$languages[$i]['name'] = $lang_info['name'];
		$languages[$i]['description'] = $lang_info['description'];
		$languages[$i]['author'] = $lang_info['author'];
		$languages[$i]['version'] = $lang_info['version'];
		$languages[$i]['action'] = CONFIG_LANG == $directories[$i] ? '<img src="' . ROOT_DIR . 'images/crystal/16/active.png" alt="" />' : '<a href="' . uri('system/entry/action_acp_languages/dir_' . $directories[$i]) . '"><img src="' . ROOT_DIR . 'images/crystal/16/inactive.png" alt="" /></a>';
	}
}
$tpl->assign('languages', $languages);

$content = $tpl->fetch('system/acp_languages.html');
?>