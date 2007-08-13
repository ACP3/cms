<?php
/**
 * Gallery
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;

$date = ' AND (start = end AND start <= \'' . date_aligned(2, time()) . '\' OR start != end AND start <= \'' . date_aligned(2, time()) . '\' AND end >= \'' . date_aligned(2, time()) . '\')';

if (!empty($modules->id) && $db->select('id', 'gallery', 'id = \'' . $modules->id . '\'' . $date, 0, 0, 0, 1) == 1) {
	// Cache für die jeweilige Galerie
	if (!$cache->check('gallery_pics_id_' . $modules->id)) {
		$cache->create('gallery_pics_id_' . $modules->id, $db->query('SELECT g.name, p.id FROM ' . CONFIG_DB_PRE . 'gallery g LEFT JOIN ' . CONFIG_DB_PRE . 'galpics p ON g.id=\'' . $modules->id . '\' AND p.gallery=\'' . $modules->id . '\' ORDER BY p.pic ASC, p.id ASC'));
	}
	$gallery = $cache->output('gallery_pics_id_' . $modules->id);

	if (count($gallery) > 0 && !empty($gallery[0]['id'])) {
		// Brotkrümelspur
		$breadcrumb->assign(lang('gallery', 'gallery'), uri('gallery'));
		$breadcrumb->assign($gallery[0]['name']);

		$tpl->assign('gallery', $gallery);
	}
	$content = $tpl->fetch('gallery/pics.html');
} else {
	redirect('errors/404');
}
?>