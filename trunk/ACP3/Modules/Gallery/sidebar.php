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

$settings = ACP3\Core\Config::getSettings('gallery');

$where = 'start = end AND start <= :time OR start != end AND :time BETWEEN start AND end';
$galleries = ACP3\CMS::$injector['Db']->fetchAll('SELECT id, start, title FROM ' . DB_PRE . 'gallery WHERE ' . $where . ' ORDER BY start DESC LIMIT ' . $settings['sidebar'], array('time' => ACP3\CMS::$injector['Date']->getCurrentDateTime()));
$c_galleries = count($galleries);

if ($c_galleries > 0) {
	for ($i = 0; $i < $c_galleries; ++$i) {
		$galleries[$i]['start'] = ACP3\CMS::$injector['Date']->format($galleries[$i]['start']);
		$galleries[$i]['title_short'] = ACP3\Core\Functions::shortenEntry($galleries[$i]['title'], 30, 5, '...');
	}
	ACP3\CMS::$injector['View']->assign('sidebar_galleries', $galleries);
}

ACP3\CMS::$injector['View']->displayTemplate('gallery/sidebar.tpl');