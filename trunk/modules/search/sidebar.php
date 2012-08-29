<?php
/**
 * Search
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */
if (defined('IN_ACP3') === false)
	exit;

$mods = scandir(MODULES_DIR);
$c_mods = count($mods);
$search_mods = array();

for ($i = 0; $i < $c_mods; ++$i) {
	if (ACP3_Modules::check($mods[$i], 'extensions/search') === true) {
		$info = ACP3_Modules::getModuleInfo($mods[$i]);
		$name = $info['name'];
		$search_mods[$name]['dir'] = $mods[$i];
		$search_mods[$name]['checked'] = selectEntry('mods', $mods[$i], $mods[$i], 'checked');
		$search_mods[$name]['name'] = $name;
	}
}
ksort($search_mods);
$tpl->assign('search_mods', $search_mods);

ACP3_View::displayTemplate('search/sidebar.tpl');