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
	require_once MODULES_DIR . 'gallery/functions.php';

	// Cache der galerie holen
	$pictures = getGalleryCache($uri->id);
	$c_pictures = count($pictures);

	if ($c_pictures > 0) {
		$gallery_name = $db->select('name', 'gallery', 'id = \'' . $uri->id . '\'');

		// Brotkrümelspur
		breadcrumb::assign($lang->t('gallery', 'gallery'), $uri->route('gallery'));
		breadcrumb::assign($gallery_name[0]['name']);

		$settings = config::getModuleSettings('gallery');

		for ($i = 0; $i < $c_pictures; ++$i) {
			$pictures[$i]['uri'] = $settings['colorbox'] == 1 ? $uri->route('gallery/image/id_' . $pictures[$i]['id'] . '/action_normal') : $uri->route('gallery/details/id_' . $pictures[$i]['id'], 1);
			$pictures[$i]['description'] = strip_tags($pictures[$i]['description']);
		}

		$tpl->assign('pictures', $pictures);
		$tpl->assign('colorbox', (int) $settings['colorbox']);
	}
	$content = modules::fetchTemplate('gallery/pics.html');
} else {
	$uri->redirect('errors/404');
}
