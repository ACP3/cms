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

$time = $date->getCurrentDateTime();
$period = ' AND (start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\')';

if (ACP3_Validate::isNumber($uri->id) === true && $db->countRows('*', 'gallery', 'id = \'' . $uri->id . '\'' . $period) == 1) {
	require_once MODULES_DIR . 'gallery/functions.php';

	// Cache der galerie holen
	$pictures = getGalleryCache($uri->id);
	$c_pictures = count($pictures);

	if ($c_pictures > 0) {
		$gallery_name = $db->select('name', 'gallery', 'id = \'' . $uri->id . '\'');
		$gallery_name[0]['name'] = $db->escape($gallery_name[0]['name'], 3);

		// Brotkrümelspur
		$breadcrumb->append($lang->t('gallery', 'gallery'), $uri->route('gallery'))
				   ->append($gallery_name[0]['name']);

		$settings = ACP3_Config::getSettings('gallery');

		for ($i = 0; $i < $c_pictures; ++$i) {
			$pictures[$i]['uri'] = $settings['overlay'] == 1 ? $uri->route('gallery/image/id_' . $pictures[$i]['id'] . '/action_normal') : $uri->route('gallery/details/id_' . $pictures[$i]['id'], 1);
			$pictures[$i]['description'] = strip_tags($db->escape($pictures[$i]['description'], 3));
		}

		$tpl->assign('pictures', $pictures);
		$tpl->assign('overlay', (int) $settings['overlay']);
	}
	ACP3_View::setContent(ACP3_View::fetchTemplate('gallery/pics.tpl'));
} else {
	$uri->redirect('errors/404');
}
