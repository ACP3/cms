<?php
/**
 * Gallery
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

$time = $date->timestamp();
$period = ' AND (g.start = g.end AND g.start <= ' . $time . ' OR g.start != g.end AND g.start <= ' . $time . ' AND g.end >= ' . $time . ')';

if (validate::isNumber($uri->id) === true && $db->select('COUNT(g.id)', 'gallery AS g, {pre}gallery_pictures AS p', 'p.id = \'' . $uri->id . '\' AND p.gallery_id = g.id' . $period) > 0) {
	$picture = $db->select('g.id AS gallery_id, g.name, p.id, p.pic, p.description, p.comments', 'gallery AS g, {pre}gallery_pictures AS p', 'p.id = \'' . $uri->id . '\' AND p.gallery_id = g.id');
	$picture[0]['description'] = $db->escape($picture[0]['description'], 3);

	$settings = config::getModuleSettings('gallery');

	// Brotkrümelspur
	$breadcrumb->append($lang->t('gallery', 'gallery'), $uri->route('gallery'))
			   ->append($picture[0]['name'], $uri->route('gallery/pics/id_' . $picture[0]['gallery_id']))
			   ->append($lang->t('gallery', 'details'));

	$tpl->assign('picture', $picture[0]);

	// Vorheriges Bild
	$picture_back = $db->select('id', 'gallery_pictures', 'pic < \'' . $picture[0]['pic'] . '\' AND gallery_id = \'' . $picture[0]['gallery_id'] . '\'', 'pic DESC', 1);
	if (count($picture_back) > 0) {
		seo::setPreviousPage($uri->route('gallery/details/id_' . $picture_back[0]['id'], 1));
		$tpl->assign('picture_back', $picture_back[0]);
	}

	// Nächstes Bild
	$picture_next = $db->select('id', 'gallery_pictures', 'pic > \'' . $picture[0]['pic'] . '\' AND gallery_id = \'' . $picture[0]['gallery_id'] . '\'', 'pic ASC', 1);
	if (count($picture_next) > 0) {
		seo::setNextPage($uri->route('gallery/details/id_' . $picture_next[0]['id'], 1));
		$tpl->assign('picture_next', $picture_next[0]);
	}

	if ($settings['overlay'] == 0 && $settings['comments'] == 1 && $picture[0]['comments'] == 1 && modules::check('comments', 'functions') === true) {
		require_once MODULES_DIR . 'comments/functions.php';

		$tpl->assign('comments', commentsList('gallery', $uri->id));
	}

	view::setContent(view::fetchTemplate('gallery/details.tpl'));
} else {
	$uri->redirect('errors/404');
}