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
	ACP3_CMS::$db2->fetchColumn('SELECT g.id FROM ' . DB_PRE . 'gallery AS g, ' . DB_PRE . 'gallery_pictures AS p WHERE p.id = :id AND p.gallery_id = g.id' . $period, array('id' => ACP3_CMS::$uri->id, 'time' => ACP3_CMS::$date->getCurrentDateTime())) > 0) {
	$picture = ACP3_CMS::$db2->fetchAssoc('SELECT g.id AS gallery_id, g.name, p.id, p.pic, p.file, p.description, p.comments FROM ' . DB_PRE . 'gallery AS g, ' . DB_PRE . 'gallery_pictures AS p WHERE p.id = ? AND p.gallery_id = g.id', array(ACP3_CMS::$uri->id));

	$settings = ACP3_Config::getSettings('gallery');

	// Brotkrümelspur
	ACP3_CMS::$breadcrumb
	->append(ACP3_CMS::$lang->t('gallery', 'gallery'), ACP3_CMS::$uri->route('gallery'))
	->append($picture['name'], ACP3_CMS::$uri->route('gallery/pics/id_' . $picture['gallery_id']))
	->append(ACP3_CMS::$lang->t('gallery', 'details'))
	->setTitlePrefix($picture['name'])
	->setTitlePostfix(sprintf(ACP3_CMS::$lang->t('gallery', 'picture_x'), $picture['pic']));

	// Bildabmessungen berechnen
	$picInfos = getimagesize(UPLOADS_DIR . 'gallery/' . $picture['file']);
	if ($picInfos[0] > $settings['width'] || $picInfos[1] > $settings['height']) {
		if ($picInfos[0] > $picInfos[1]) {
			$newWidth = $settings['width'];
			$newHeight = intval($picInfos[1] * $newWidth / $picInfos[0]);
		} else {
			$newHeight = $settings['height'];
			$newWidth = intval($picInfos[0] * $newHeight / $picInfos[1]);
		}
	}

	$picture['width'] = isset($newWidth) ? $newWidth : $picInfos[0];
	$picture['height'] = isset($newHeight) ? $newHeight : $picInfos[1];

	ACP3_CMS::$view->assign('picture', $picture);

	// Vorheriges Bild
	$picture_back = ACP3_CMS::$db2->fetchColumn('SELECT id FROM ' . DB_PRE . 'gallery_pictures WHERE pic < ? AND gallery_id = ? ORDER BY pic DESC LIMIT 1', array($picture['pic'], $picture['gallery_id']));
	if (!empty($picture_back)) {
		ACP3_SEO::setPreviousPage(ACP3_CMS::$uri->route('gallery/details/id_' . $picture_back));
		ACP3_CMS::$view->assign('picture_back', $picture_back);
	}

	// Nächstes Bild
	$picture_next = ACP3_CMS::$db2->fetchColumn('SELECT id FROM ' . DB_PRE . 'gallery_pictures WHERE pic > ? AND gallery_id = ? ORDER BY pic ASC LIMIT 1', array($picture['pic'], $picture['gallery_id']));
	if (!empty($picture_next)) {
		ACP3_SEO::setNextPage(ACP3_CMS::$uri->route('gallery/details/id_' . $picture_next));
		ACP3_CMS::$view->assign('picture_next', $picture_next);
	}

	if ($settings['overlay'] == 0 && $settings['comments'] == 1 && $picture['comments'] == 1 && ACP3_Modules::check('comments', 'functions') === true) {
		require_once MODULES_DIR . 'comments/functions.php';

		ACP3_CMS::$view->assign('comments', commentsList('gallery', ACP3_CMS::$uri->id));
	}

	ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('gallery/details.tpl'));
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}