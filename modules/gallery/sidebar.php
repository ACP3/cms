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

$where = 'start = end AND start <= :time OR start != end AND :time BETWEEN start AND end';
$galleries = ACP3_CMS::$db2->fetchAll('SELECT id, start, name FROM ' . DB_PRE . 'gallery WHERE ' . $where . ' ORDER BY start DESC LIMIT ' . $settings['sidebar'], array('time' => ACP3_CMS::$date->getCurrentDateTime()));
$c_galleries = count($galleries);

if ($c_galleries > 0) {
	for ($i = 0; $i < $c_galleries; ++$i) {
		$galleries[$i]['start'] = ACP3_CMS::$date->format($galleries[$i]['start']);
		$galleries[$i]['name_short'] = shortenEntry($galleries[$i]['name'], 30, 5, '...');
	}
	ACP3_CMS::$view->assign('sidebar_galleries', $galleries);
}

ACP3_CMS::$view->displayTemplate('gallery/sidebar.tpl');