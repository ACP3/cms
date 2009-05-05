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

if (validate::isNumber($uri->id) && $db->countRows('*', 'gallery', 'id = \'' . $uri->id . '\'' . $period) == 1) {
	// Cache der galerie holen
	$gallery = getGalleryCache($uri->id);

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