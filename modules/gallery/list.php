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

$time = ACP3_CMS::$date->getCurrentDateTime();
$where = '(start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\')';
$galleries = ACP3_CMS::$db->select('id, start, name', 'gallery', $where, 'start DESC, end DESC, id DESC', POS, ACP3_CMS::$auth->entries);
$c_galleries = count($galleries);

if ($c_galleries > 0) {
	ACP3_CMS::$view->assign('pagination', pagination(ACP3_CMS::$db->countRows('*', 'gallery', $where)));

	$settings = ACP3_Config::getSettings('gallery');

	for ($i = 0; $i < $c_galleries; ++$i) {
		$galleries[$i]['date'] = ACP3_CMS::$date->format($galleries[$i]['start'], $settings['dateformat']);
		$galleries[$i]['name'] = ACP3_CMS::$db->escape($galleries[$i]['name'], 3);
		$pictures = ACP3_CMS::$db->countRows('*', 'gallery_pictures', 'gallery_id = \'' . $galleries[$i]['id'] . '\'');
		$galleries[$i]['pics'] = $pictures == 1 ? '1 ' . ACP3_CMS::$lang->t('gallery', 'picture') : $pictures . ' ' . ACP3_CMS::$lang->t('gallery', 'pictures');
	}
	ACP3_CMS::$view->assign('galleries', $galleries);
}
ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('gallery/list.tpl'));
