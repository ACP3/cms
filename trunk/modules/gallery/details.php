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
$period = ' AND (g.start = g.end AND g.start <= \'' . $time . '\' OR g.start != g.end AND g.start <= \'' . $time . '\' AND g.end >= \'' . $time . '\')';

if (validate::isNumber($uri->id) && $db->select('COUNT(g.id)', 'gallery AS g, ' . $db->prefix . 'gallery_pictures AS p', 'p.id = \'' . $uri->id . '\' AND p.gallery_id = g.id' . $period) > 0) {
	$picture = $db->select('g.id AS gallery_id, g.name, p.id, p.pic, p.description, p.comments', 'gallery AS g, ' . $db->prefix . 'gallery_pictures AS p', 'p.id = \'' . $uri->id . '\' AND p.gallery_id = g.id');
	$settings = config::output('gallery');

	// Brotkrümelspur
	breadcrumb::assign($lang->t('gallery', 'gallery'), uri('gallery'));
	breadcrumb::assign($picture[0]['name'], uri('gallery/pics/id_' . $picture[0]['gallery_id']));
	breadcrumb::assign($lang->t('gallery', 'details'));

	$picture[0]['description'] = $db->escape($picture[0]['description'], 3);
	$tpl->assign('picture', $picture[0]);

	$picture_back = $db->select('id', 'gallery_pictures', 'pic < \'' . $picture[0]['pic'] . '\' AND gallery_id = \'' . $picture[0]['gallery_id'] . '\'', 'pic DESC', 1);
	$picture_next = $db->select('id', 'gallery_pictures', 'pic > \'' . $picture[0]['pic'] . '\' AND gallery_id = \'' . $picture[0]['gallery_id'] . '\'', 'pic ASC', 1);

	// Vorheriges Bild
	if (count($picture_back) > 0)
		$tpl->assign('picture_back', $picture_back[0]);
	// Nächstes Bild
	if (count($picture_next) > 0)
		$tpl->assign('picture_next', $picture_next[0]);

	if ($settings['colorbox'] == 0 && $settings['comments'] == 1 && $picture[0]['comments'] == 1 && modules::check('comments', 'functions') == 1) {
		require_once ACP3_ROOT . 'modules/comments/functions.php';

		$tpl->assign('comments', commentsList('gallery', $uri->id));
	}

	$content = modules::fetchTemplate('gallery/details.html');
} else {
	redirect('errors/404');
}
