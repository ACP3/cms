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

$where = 'start = end AND start <= :time OR start != end AND :time BETWEEN start AND end';
$files = ACP3_CMS::$db2->fetchAll('SELECT id, start, link_title FROM ' . DB_PRE . 'files WHERE ' . $where . ' ORDER BY start DESC LIMIT ' . $settings['sidebar'], array('time' => ACP3_CMS::$date->getCurrentDateTime()));
$c_files = count($files);

if ($c_files > 0) {
	for ($i = 0; $i < $c_files; ++$i) {
		$files[$i]['start'] = ACP3_CMS::$date->format($files[$i]['start']);
		$files[$i]['link_title_short'] = shortenEntry($files[$i]['link_title'], 30, 5, '...');
	}
	ACP3_CMS::$view->assign('sidebar_files', $files);
}

ACP3_CMS::$view->displayTemplate('files/sidebar.tpl');
