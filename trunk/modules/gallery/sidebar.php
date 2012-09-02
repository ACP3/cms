<?php
/**
 * Gallery
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */
if (defined('IN_ACP3') === false)
	exit;

$settings = ACP3_Config::getSettings('gallery');

$time = ACP3_CMS::$date->getCurrentDateTime();
$where = 'start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\'';
$galleries = ACP3_CMS::$db->select('id, start, name', 'gallery', $where, 'start DESC', $settings['sidebar']);
$c_galleries = count($galleries);

if ($c_galleries > 0) {
	for ($i = 0; $i < $c_galleries; ++$i) {
		$galleries[$i]['start'] = ACP3_CMS::$date->format($galleries[$i]['start']);
		$galleries[$i]['name'] = ACP3_CMS::$db->escape($galleries[$i]['name'], 3);
		$galleries[$i]['name_short'] = shortenEntry($galleries[$i]['name'], 30, 5, '...');
	}
	ACP3_CMS::$view->assign('sidebar_galleries', $galleries);
}

ACP3_CMS::$view->displayTemplate('gallery/sidebar.tpl');