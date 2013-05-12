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
if (ACP3\Core\Validate::isNumber(ACP3\CMS::$injector['URI']->id) === true &&
	ACP3\CMS::$injector['Db']->fetchColumn('SELECT g.id FROM ' . DB_PRE . 'gallery AS g, ' . DB_PRE . 'gallery_pictures AS p WHERE p.id = :id AND p.gallery_id = g.id' . $period, array('id' => ACP3\CMS::$injector['URI']->id, 'time' => ACP3\CMS::$injector['Date']->getCurrentDateTime())) > 0) {
	$picture = ACP3\CMS::$injector['Db']->fetchAssoc('SELECT g.id AS gallery_id, g.title, p.id, p.pic, p.file, p.description, p.comments FROM ' . DB_PRE . 'gallery AS g, ' . DB_PRE . 'gallery_pictures AS p WHERE p.id = ? AND p.gallery_id = g.id', array(ACP3\CMS::$injector['URI']->id));

	$settings = ACP3\Core\Config::getSettings('gallery');

	// Brotkrümelspur
	ACP3\CMS::$injector['Breadcrumb']
	->append(ACP3\CMS::$injector['Lang']->t('gallery', 'gallery'), ACP3\CMS::$injector['URI']->route('gallery'))
	->append($picture['title'], ACP3\CMS::$injector['URI']->route('gallery/pics/id_' . $picture['gallery_id']))
	->append(ACP3\CMS::$injector['Lang']->t('gallery', 'details'))
	->setTitlePrefix($picture['title'])
	->setTitlePostfix(sprintf(ACP3\CMS::$injector['Lang']->t('gallery', 'picture_x'), $picture['pic']));

	// Bildabmessungen berechnen
	$picture['width'] = $settings['width'];
	$picture['height'] = $settings['height'];
	$picInfos = @getimagesize(UPLOADS_DIR . 'gallery/' . $picture['file']);
	if ($picInfos !== false) {
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
	}

	ACP3\CMS::$injector['View']->assign('picture', $picture);

	// Vorheriges Bild
	$picture_back = ACP3\CMS::$injector['Db']->fetchColumn('SELECT id FROM ' . DB_PRE . 'gallery_pictures WHERE pic < ? AND gallery_id = ? ORDER BY pic DESC LIMIT 1', array($picture['pic'], $picture['gallery_id']));
	if (!empty($picture_back)) {
		ACP3\Core\SEO::setPreviousPage(ACP3\CMS::$injector['URI']->route('gallery/details/id_' . $picture_back));
		ACP3\CMS::$injector['View']->assign('picture_back', $picture_back);
	}

	// Nächstes Bild
	$picture_next = ACP3\CMS::$injector['Db']->fetchColumn('SELECT id FROM ' . DB_PRE . 'gallery_pictures WHERE pic > ? AND gallery_id = ? ORDER BY pic ASC LIMIT 1', array($picture['pic'], $picture['gallery_id']));
	if (!empty($picture_next)) {
		ACP3\Core\SEO::setNextPage(ACP3\CMS::$injector['URI']->route('gallery/details/id_' . $picture_next));
		ACP3\CMS::$injector['View']->assign('picture_next', $picture_next);
	}

	if ($settings['overlay'] == 0 && $settings['comments'] == 1 && $picture['comments'] == 1 && ACP3\Core\Modules::check('comments', 'functions') === true) {
		require_once MODULES_DIR . 'comments/functions.php';

		ACP3\CMS::$injector['View']->assign('comments', commentsList('gallery', ACP3\CMS::$injector['URI']->id));
	}
} else {
	ACP3\CMS::$injector['URI']->redirect('errors/404');
}