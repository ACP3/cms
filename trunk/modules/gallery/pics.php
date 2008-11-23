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

$time = $date->timestamp();
$period = ' AND (start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\')';

if (validate::isNumber($uri->id) && $db->select('id', 'gallery', 'id = \'' . $uri->id . '\'' . $period, 0, 0, 0, 1) == 1) {
	// Cache für die jeweilige Galerie
	if (!cache::check('gallery_pics_id_' . $uri->id)) {
		cache::create('gallery_pics_id_' . $uri->id, $db->select('id', 'gallery_pictures', 'gallery_id = \'' . $uri->id . '\'', 'pic ASC, id ASC'));
	}
	$gallery = cache::output('gallery_pics_id_' . $uri->id);

	if (count($gallery) > 0 ) {
		$gallery_name = $db->select('name', 'gallery', 'id = \'' . $uri->id . '\'');

		// Brotkrümelspur
		breadcrumb::assign($lang->t('gallery', 'gallery'), uri('gallery'));
		breadcrumb::assign($gallery_name[0]['name']);

		$tpl->assign('gallery', $gallery);
	}
	$content = $tpl->fetch('gallery/pics.html');
} else {
	redirect('errors/404');
}
?>