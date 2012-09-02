<?php
/**
 * Files
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */
if (defined('IN_ACP3') === false)
	exit;

$settings = ACP3_Config::getSettings('files');

$time = ACP3_CMS::$date->getCurrentDateTime();
$where = 'start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\'';
$files = ACP3_CMS::$db->select('id, start, link_title', 'files', $where, 'start DESC', $settings['sidebar']);
$c_files = count($files);

if ($c_files > 0) {
	for ($i = 0; $i < $c_files; ++$i) {
		$files[$i]['start'] = ACP3_CMS::$date->format($files[$i]['start']);
		$files[$i]['link_title'] = ACP3_CMS::$db->escape($files[$i]['link_title'], 3);
		$files[$i]['link_title_short'] = shortenEntry($files[$i]['link_title'], 30, 5, '...');
	}
	ACP3_CMS::$view->assign('sidebar_files', $files);
}

ACP3_CMS::$view->displayTemplate('files/sidebar.tpl');
