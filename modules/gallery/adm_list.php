<?php
/**
 * Gallery
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

$galleries = $db->select('id, start, end, name', 'gallery', 0, 'start DESC, end DESC, id DESC', POS, CONFIG_ENTRIES);
$c_galleries = count($galleries);

if ($c_galleries > 0) {
	$tpl->assign('pagination', pagination($db->select('id', 'gallery', 0, 0, 0, 0, 1)));
	for ($i = 0; $i < $c_galleries; ++$i) {
		$galleries[$i]['period'] = $date->period($galleries[$i]['start'], $galleries[$i]['end']);
		$galleries[$i]['name'] = $galleries[$i]['name'];
		$galleries[$i]['pictures'] = $db->select('DISTINCT id', 'gallery_pictures', 'gallery_id = \'' . $galleries[$i]['id'] . '\'', 0, 0, 0, 1);
	}
	$tpl->assign('galleries', $galleries);
}
$content = $tpl->fetch('gallery/adm_list.html');
?>