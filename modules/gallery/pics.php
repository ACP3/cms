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

$period = ' AND (start = end AND start <= :time OR start != end AND :time BETWEEN start AND end)';
if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true &&
	ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery WHERE id = :id' . $period, array('id' => ACP3_CMS::$uri->id, 'time' => ACP3_CMS::$date->getCurrentDateTime())) == 1) {
	require_once MODULES_DIR . 'gallery/functions.php';

	// Cache der Galerie holen
	$pictures = getGalleryCache(ACP3_CMS::$uri->id);
	$c_pictures = count($pictures);

	if ($c_pictures > 0) {
		$gallery_name = ACP3_CMS::$db2->fetchColumn('SELECT name FROM ' . DB_PRE . 'gallery WHERE id = ?', array(ACP3_CMS::$uri->id));

		// Brotkrümelspur
		ACP3_CMS::$breadcrumb
		->append(ACP3_CMS::$lang->t('gallery', 'gallery'), ACP3_CMS::$uri->route('gallery'))
		->append($gallery_name);

		$settings = ACP3_Config::getSettings('gallery');

		for ($i = 0; $i < $c_pictures; ++$i) {
			$pictures[$i]['uri'] = ACP3_CMS::$uri->route($settings['overlay'] == 1 ? 'gallery/image/id_' . $pictures[$i]['id'] . '/action_normal' : 'gallery/details/id_' . $pictures[$i]['id']);
			$pictures[$i]['description'] = strip_tags($pictures[$i]['description']);
		}

		ACP3_CMS::$view->assign('pictures', $pictures);
		ACP3_CMS::$view->assign('overlay', (int) $settings['overlay']);
	}
	ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('gallery/pics.tpl'));
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}
