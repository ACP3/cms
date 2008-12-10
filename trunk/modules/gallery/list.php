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
$where = '(start = end AND start <= \'' . $time . '\' OR start != end AND start <= \'' . $time . '\' AND end >= \'' . $time . '\')';
$galleries = $db->select('id, start, name', 'gallery', $where, 'start DESC, end DESC, id DESC', POS, CONFIG_ENTRIES);
$c_galleries = count($galleries);

if ($c_galleries > 0) {
	$tpl->assign('pagination', pagination($db->select('COUNT(id)', 'gallery', $where, 0, 0, 0, 1)));

	for ($i = 0; $i < $c_galleries; ++$i) {
		$galleries[$i]['date'] = $date->format($galleries[$i]['start']);
		$galleries[$i]['name'] = $galleries[$i]['name'];
		$pictures = $db->select('DISTINCT id', 'gallery_pictures', 'gallery_id = \'' . $galleries[$i]['id'] . '\'', 0, 0, 0, 1);
		$galleries[$i]['pics'] = $pictures == '1' ? '1 ' . $lang->t('gallery', 'picture') : $pictures . ' ' . $lang->t('gallery', 'pictures');
	}
	$tpl->assign('galleries', $galleries);
}
$content = $tpl->fetch('gallery/list.html');
?>