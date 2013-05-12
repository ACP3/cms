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

$settings = ACP3\Core\Config::getSettings('files');

$where = 'start = end AND start <= :time OR start != end AND :time BETWEEN start AND end';
$files = ACP3\CMS::$injector['Db']->fetchAll('SELECT id, start, title FROM ' . DB_PRE . 'files WHERE ' . $where . ' ORDER BY start DESC LIMIT ' . $settings['sidebar'], array('time' => ACP3\CMS::$injector['Date']->getCurrentDateTime()));
$c_files = count($files);

if ($c_files > 0) {
	for ($i = 0; $i < $c_files; ++$i) {
		$files[$i]['start'] = ACP3\CMS::$injector['Date']->format($files[$i]['start']);
		$files[$i]['title_short'] = ACP3\Core\Functions::shortenEntry($files[$i]['title'], 30, 5, '...');
	}
	ACP3\CMS::$injector['View']->assign('sidebar_files', $files);
}

ACP3\CMS::$injector['View']->displayTemplate('files/sidebar.tpl');
