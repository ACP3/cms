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
$period = ' AND (start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\')';

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true && ACP3_CMS::$db->countRows('*', 'gallery', 'id = \'' . ACP3_CMS::$uri->id . '\'' . $period) == 1) {
	require_once MODULES_DIR . 'gallery/functions.php';

	// Cache der galerie holen
	$pictures = getGalleryCache(ACP3_CMS::$uri->id);
	$c_pictures = count($pictures);

	if ($c_pictures > 0) {
		$gallery_name = ACP3_CMS::$db->select('name', 'gallery', 'id = \'' . ACP3_CMS::$uri->id . '\'');
		$gallery_name[0]['name'] = ACP3_CMS::$db->escape($gallery_name[0]['name'], 3);

		// Brotkrümelspur
		ACP3_CMS::$breadcrumb->append(ACP3_CMS::$lang->t('gallery', 'gallery'), ACP3_CMS::$uri->route('gallery'))
				   ->append($gallery_name[0]['name']);

		$settings = ACP3_Config::getSettings('gallery');

		for ($i = 0; $i < $c_pictures; ++$i) {
			$pictures[$i]['uri'] = $settings['overlay'] == 1 ? ACP3_CMS::$uri->route('gallery/image/id_' . $pictures[$i]['id'] . '/action_normal') : ACP3_CMS::$uri->route('gallery/details/id_' . $pictures[$i]['id'], 1);
			$pictures[$i]['description'] = strip_tags(ACP3_CMS::$db->escape($pictures[$i]['description'], 3));
		}

		ACP3_CMS::$view->assign('pictures', $pictures);
		ACP3_CMS::$view->assign('overlay', (int) $settings['overlay']);
	}
	ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('gallery/pics.tpl'));
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}
