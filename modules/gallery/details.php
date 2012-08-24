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

if (ACP3_Validate::isNumber($uri->id) === true && $db->countRows('g.id', 'gallery AS g, {pre}gallery_pictures AS p', 'p.id = \'' . $uri->id . '\' AND p.gallery_id = g.id' . $period) > 0) {
	$picture = $db->select('g.id AS gallery_id, g.name, p.id, p.pic, p.file, p.description, p.comments', 'gallery AS g, {pre}gallery_pictures AS p', 'p.id = \'' . $uri->id . '\' AND p.gallery_id = g.id');
	$picture[0]['description'] = $db->escape($picture[0]['description'], 3);

	$settings = ACP3_Config::getSettings('gallery');

	// Brotkrümelspur
	$breadcrumb->append($lang->t('gallery', 'gallery'), $uri->route('gallery'))
			   ->append($picture[0]['name'], $uri->route('gallery/pics/id_' . $picture[0]['gallery_id']))
			   ->append($lang->t('gallery', 'details'));

	// Bildabmessungen berechnen
	$picInfos = getimagesize(ACP3_ROOT . 'uploads/gallery/' . $picture[0]['file']);
	if ($picInfos[0] > $settings['width'] || $picInfos[1] > $settings['height']) {
		if ($picInfos[0] > $picInfos[1]) {
			$newWidth = $settings['width'];
			$newHeight = intval($picInfos[1] * $newWidth / $picInfos[0]);
		} else {
			$newHeight = $settings['height'];
			$newWidth = intval($picInfos[0] * $newHeight / $picInfos[1]);
		}
	}

	$picture[0]['width'] = isset($newWidth) ? $newWidth : $picInfos[0];
	$picture[0]['height'] = isset($newHeight) ? $newHeight : $picInfos[1];

	$tpl->assign('picture', $picture[0]);

	// Vorheriges Bild
	$picture_back = $db->select('id', 'gallery_pictures', 'pic < \'' . $picture[0]['pic'] . '\' AND gallery_id = \'' . $picture[0]['gallery_id'] . '\'', 'pic DESC', 1);
	if (count($picture_back) > 0) {
		ACP3_SEO::setPreviousPage($uri->route('gallery/details/id_' . $picture_back[0]['id'], 1));
		$tpl->assign('picture_back', $picture_back[0]);
	}

	// Nächstes Bild
	$picture_next = $db->select('id', 'gallery_pictures', 'pic > \'' . $picture[0]['pic'] . '\' AND gallery_id = \'' . $picture[0]['gallery_id'] . '\'', 'pic ASC', 1);
	if (count($picture_next) > 0) {
		ACP3_SEO::setNextPage($uri->route('gallery/details/id_' . $picture_next[0]['id'], 1));
		$tpl->assign('picture_next', $picture_next[0]);
	}

	if ($settings['overlay'] == 0 && $settings['comments'] == 1 && $picture[0]['comments'] == 1 && ACP3_Modules::check('comments', 'functions') === true) {
		require_once MODULES_DIR . 'comments/functions.php';

		$tpl->assign('comments', commentsList('gallery', $uri->id));
	}

	ACP3_View::setContent(ACP3_View::fetchTemplate('gallery/details.tpl'));
} else {
	$uri->redirect('errors/404');
}