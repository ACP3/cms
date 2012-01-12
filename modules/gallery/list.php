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

$time = $date->timestamp();
$where = '(start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\')';
$galleries = $db->select('id, start, name', 'gallery', $where, 'start DESC, end DESC, id DESC', POS, $auth->entries);
$c_galleries = count($galleries);

if ($c_galleries > 0) {
	$tpl->assign('pagination', pagination($db->countRows('*', 'gallery', $where)));

	$settings = config::getModuleSettings('gallery');

	for ($i = 0; $i < $c_galleries; ++$i) {
		$galleries[$i]['date'] = $date->format($galleries[$i]['start'], $settings['dateformat']);
		$galleries[$i]['name'] = $db->escape($galleries[$i]['name'], 3);
		$pictures = $db->countRows('*', 'gallery_pictures', 'gallery_id = \'' . $galleries[$i]['id'] . '\'');
		$galleries[$i]['pics'] = $pictures == '1' ? '1 ' . $lang->t('gallery', 'picture') : $pictures . ' ' . $lang->t('gallery', 'pictures');
	}
	$tpl->assign('galleries', $galleries);
}
$content = modules::fetchTemplate('gallery/list.tpl');
