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

$date = " AND ((g.start = g.end AND g.start<='" . date_aligned(2, time()) . "') OR (g.start != g.end AND g.start<='" . date_aligned(2, time()) . "' AND g.end>='" . date_aligned(2, time()) . "'))";

if (!empty($modules->id) && $db->select('g.id', 'gallery AS g, ' . CONFIG_DB_PRE . 'galpics AS p', 'p.id = \'' . $modules->id . '\' AND p.gallery = g.id' . $date, 0, 0, 0, 1) == 1) {
	$picture = $db->select('g.id AS gallery_id, g.name, p.id, p.pic, p.gallery, p.description', 'gallery AS g, ' . CONFIG_DB_PRE . 'galpics AS p', 'p.id = \'' . $modules->id . '\' AND p.gallery = g.id');

	if (count($picture) > 0) {
		// Brotkrümelspur
		$breadcrumb->assign(lang('gallery', 'gallery'), uri('gallery'));
		$breadcrumb->assign($picture[0]['name'], uri('gallery/pics/id_' . $picture[0]['gallery_id']));
		$breadcrumb->assign(lang('gallery', 'details'));

		$picture[0]['description'] = $db->escape($picture[0]['description'], 3);
		$tpl->assign('picture', $picture[0]);

		$picture_back = $db->select('id', 'galpics', 'pic < \'' . $picture[0]['pic'] . '\' AND gallery = \'' . $picture[0]['gallery'] . '\'', 'pic DESC', 1);
		$picture_next = $db->select('id', 'galpics', 'pic > \'' . $picture[0]['pic'] . '\' AND gallery = \'' . $picture[0]['gallery'] . '\'', 'pic ASC', 1);

		if (count($picture_back) > 0)
			$tpl->assign('picture_back', $picture_back[0]);

		if (count($picture_next) > 0)
			$tpl->assign('picture_next', $picture_next[0]);
	}
	$content = $tpl->fetch('gallery/details.html');
} else {
	redirect('errors/404');
}
?>