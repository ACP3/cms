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

// Sprache
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
		$languages[$i]['action'] = CONFIG_LANG == $directories[$i] ? '<img src="' . ROOT_DIR . 'images/crystal/16/active.png" alt="" />' : '<a href="' . uri('acp/system/entry/action_lang/dir_' . $directories[$i]) . '"><img src="' . ROOT_DIR . 'images/crystal/16/inactive.png" alt="" /></a>';
	}
}
$tpl->assign('languages', $languages);

// Designs
$designs = array();
$directories = scandir('designs');
$count_dir = count($directories);
for ($i = 0; $i < $count_dir; $i++) {
	$design_info = array();
	if ($directories[$i] != '.' && $directories[$i] != '..' && file_exists('designs/' . $directories[$i] . '/info.php')) {
		include 'designs/' . $directories[$i] . '/info.php';
		$designs[$i]['name'] = $design_info['name'];
		$designs[$i]['description'] = $design_info['description'];
		$designs[$i]['author'] = $design_info['author'];
		$designs[$i]['version'] = $design_info['version'];
		$designs[$i]['action'] = CONFIG_DESIGN == $directories[$i] ? '<img src="' . ROOT_DIR . 'images/crystal/16/active.png" alt="" />' : '<a href="' . uri('acp/system/entry/action_design/dir_' . urlencode($directories[$i])) . '"><img src="' . ROOT_DIR . 'images/crystal/16/inactive.png" alt="" /></a>';
	}
}
$tpl->assign('designs', $designs);

$content = $tpl->fetch('system/lang_design.html');
?>