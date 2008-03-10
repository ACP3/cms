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

$breadcrumb->assign(lang('system', 'system'), uri('acp/system'));
$breadcrumb->assign(lang('system', 'extensions'), uri('acp/system/extensions'));
$breadcrumb->assign(lang('system', 'designs'));

if (isset($modules->gen['dir'])) {
	$dir = is_file('designs/' . $modules->gen['dir'] . '/info.php') ? $modules->gen['dir'] : 0;
	$bool = false;

	if (!empty($dir)) {
		$bool = $config->general(array('design' => $dir));
	}
	$text = $bool ? lang('system', 'designs_edit_success') : lang('system', 'designs_edit_error');

	// Cache leeren und diverse Parameter für die Template Engine abändern
	$cache->purge();
	$tpl->template_dir = './designs/' . $dir . '/';
	$tpl->assign('design_path', ROOT_DIR . 'designs/' . $dir . '/');

	$content = combo_box($text, uri('acp/system/designs'));
} else {
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
			$designs[$i]['action'] = CONFIG_DESIGN == $directories[$i] ? '<img src="' . ROOT_DIR . 'images/crystal/16/active.png" alt="" />' : '<a href="' . uri('acp/system/designs/dir_' . urlencode($directories[$i])) . '"><img src="' . ROOT_DIR . 'images/crystal/16/inactive.png" alt="" /></a>';
		}
	}
	$tpl->assign('designs', $designs);

	$content = $tpl->fetch('system/designs.html');
}
?>