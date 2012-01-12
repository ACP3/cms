<?php
/**
 * Gallery
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */
if (defined('IN_ACP3') === false)
	exit;

$settings = config::getModuleSettings('gallery');

$time = $date->timestamp();
$where = 'start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\'';
$galleries = $db->select('id, start, name', 'gallery', $where, 'start DESC', $settings['sidebar']);
$c_galleries = count($galleries);

if ($c_galleries > 0) {
	for ($i = 0; $i < $c_galleries; ++$i) {
		$galleries[$i]['start'] = $date->format($galleries[$i]['start']);
		$galleries[$i]['name'] = $db->escape($galleries[$i]['name'], 3);
		$galleries[$i]['name_short'] = shortenEntry($galleries[$i]['name'], 30, 5, '...');
	}
	$tpl->assign('sidebar_galleries', $galleries);
}

modules::displayTemplate('gallery/sidebar.tpl');