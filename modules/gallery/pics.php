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

$date = ' AND (start = end AND start <= \'' . dateAligned(2, time()) . '\' OR start != end AND start <= \'' . dateAligned(2, time()) . '\' AND end >= \'' . dateAligned(2, time()) . '\')';

if (validate::isNumber($modules->id) && $db->select('id', 'gallery', 'id = \'' . $modules->id . '\'' . $date, 0, 0, 0, 1) == 1) {
	// Cache für die jeweilige Galerie
	if (!cache::check('gallery_pics_id_' . $modules->id)) {
		cache::create('gallery_pics_id_' . $modules->id, $db->select('id', 'galpics', 'gallery_id = \'' . $modules->id . '\'', 'id ASC'));
	}
	$gallery = cache::output('gallery_pics_id_' . $modules->id);

	if (count($gallery) > 0 ) {
		$gallery_name = $db->select('name', 'gallery', 'id = \'' . $modules->id . '\'');

		// Brotkrümelspur
		$breadcrumb->assign(lang('gallery', 'gallery'), uri('gallery'));
		$breadcrumb->assign($gallery_name[0]['name']);

		$tpl->assign('gallery', $gallery);
	}
	$content = $tpl->fetch('gallery/pics.html');
} else {
	redirect('errors/404');
}
?>