<?php
/**
 * Gallery
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

ACP3\Core\Functions::getRedirectMessage();

$galleries = ACP3\CMS::$injector['Db']->fetchAll('SELECT g.id, g.start, g.end, g.title, COUNT(p.gallery_id) AS pictures FROM ' . DB_PRE . 'gallery AS g LEFT JOIN ' . DB_PRE . 'gallery_pictures AS p ON(g.id = p.gallery_id) GROUP BY g.id ORDER BY g.start DESC, g.end DESC, g.id DESC');
$c_galleries = count($galleries);

if ($c_galleries > 0) {
	$can_delete = ACP3\Core\Modules::check('gallery', 'acp_delete');
	$config = array(
		'element' => '#acp-table',
		'sort_col' => $can_delete === true ? 1 : 0,
		'sort_dir' => 'desc',
		'hide_col_sort' => $can_delete === true ? 0 : ''
	);
	ACP3\CMS::$injector['View']->appendContent(ACP3\Core\Functions::datatable($config));
	for ($i = 0; $i < $c_galleries; ++$i) {
		$galleries[$i]['period'] = ACP3\CMS::$injector['Date']->formatTimeRange($galleries[$i]['start'], $galleries[$i]['end']);
	}
	ACP3\CMS::$injector['View']->assign('galleries', $galleries);
	ACP3\CMS::$injector['View']->assign('can_delete', $can_delete);
}