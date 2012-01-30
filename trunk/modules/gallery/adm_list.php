<?php
/**
 * Gallery
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

$galleries = $db->select('id, start, end, name', 'gallery', 0, 'start DESC, end DESC, id DESC', POS, $session->get('entries'));
$c_galleries = count($galleries);

if ($c_galleries > 0) {
	$tpl->assign('pagination', pagination($db->countRows('*', 'gallery')));
	for ($i = 0; $i < $c_galleries; ++$i) {
		$galleries[$i]['period'] = $date->period($galleries[$i]['start'], $galleries[$i]['end']);
		$galleries[$i]['name'] = $db->escape($galleries[$i]['name'], 3);
		$galleries[$i]['pictures'] = $db->countRows('*', 'gallery_pictures', 'gallery_id = \'' . $galleries[$i]['id'] . '\'');
	}
	$tpl->assign('galleries', $galleries);
}
view::setContent(view::fetchTemplate('gallery/adm_list.tpl'));
