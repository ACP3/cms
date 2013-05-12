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

$time = ACP3\CMS::$injector['Date']->getCurrentDateTime();
$where = '(g.start = g.end AND g.start <= :time OR g.start != g.end AND :time BETWEEN g.start AND g.end)';
$galleries = ACP3\CMS::$injector['Db']->fetchAll('SELECT g.id, g.start, g.title, COUNT(p.gallery_id) AS pics FROM ' . DB_PRE . 'gallery AS g LEFT JOIN ' . DB_PRE . 'gallery_pictures AS p ON(g.id = p.gallery_id) WHERE ' . $where . ' GROUP BY g.id ORDER BY g.start DESC, g.end DESC, g.id DESC LIMIT ' . POS . ',' . ACP3\CMS::$injector['Auth']->entries, array('time' => $time));
$c_galleries = count($galleries);

if ($c_galleries > 0) {
	ACP3\CMS::$injector['View']->assign('pagination', ACP3\Core\Functions::pagination(ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery AS g WHERE ' . $where, array('time' => $time))));

	$settings = ACP3\Core\Config::getSettings('gallery');

	for ($i = 0; $i < $c_galleries; ++$i) {
		$galleries[$i]['date_formatted'] = ACP3\CMS::$injector['Date']->format($galleries[$i]['start'], $settings['dateformat']);
		$galleries[$i]['date_iso'] = ACP3\CMS::$injector['Date']->format($galleries[$i]['start'], 'c');
		$galleries[$i]['pics_lang'] = $galleries[$i]['pics'] . ' ' . ACP3\CMS::$injector['Lang']->t('gallery', $galleries[$i]['pics'] == 1 ? 'picture' : 'pictures');
	}
	ACP3\CMS::$injector['View']->assign('galleries', $galleries);
}