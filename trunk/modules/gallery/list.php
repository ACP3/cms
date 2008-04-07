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

$galleries = $db->select('id, start, name', 'gallery', '(start = end AND start <= \'' . dateAligned(2, time()) . '\' OR start != end AND start <= \'' . dateAligned(2, time()) . '\' AND end >= \'' . dateAligned(2, time()) . '\')', 'start DESC, id DESC', POS, CONFIG_ENTRIES);
$c_galleries = count($galleries);

if ($c_galleries > 0) {
	$tpl->assign('pagination', $modules->pagination($db->select('id', 'gallery', '(start = end AND start <= \'' . dateAligned(2, time()) . '\' OR start != end AND start <= \'' . dateAligned(2, time()) . '\' AND end >= \'' . dateAligned(2, time()) . '\')', 0, 0, 0, 1)));

	for ($i = 0; $i < $c_galleries; $i++) {
		$galleries[$i]['date'] = dateAligned(1, $galleries[$i]['start']);
		$galleries[$i]['name'] = $galleries[$i]['name'];
		$pictures = $db->select('DISTINCT id', 'galpics', 'gallery_id = \'' . $galleries[$i]['id'] . '\'', 0, 0, 0, 1);
		$galleries[$i]['pics'] = $pictures == '1' ? '1 ' . lang('gallery', 'picture') : $pictures . ' ' . lang('gallery', 'pictures');
	}
	$tpl->assign('galleries', $galleries);
}
$content = $tpl->fetch('gallery/list.html');
?>