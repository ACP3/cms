<?php
/**
 * Gallery
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP'))
	exit;

if (!empty($modules->id) && $db->select('id', 'galpics', 'id = \'' . $modules->id . '\'', 0, 0, 0, 1) == '1') {
	$picture = $db->select('p.pic, p.gallery_id, p.file, p.description, g.name AS gallery_name', 'galpics AS p, ' . CONFIG_DB_PRE . 'gallery AS g', 'p.id = \'' . $modules->id . '\' AND p.gallery_id = g.id');

	$breadcrumb->assign(lang('gallery', 'gallery'), uri('acp/gallery'));
	$breadcrumb->assign($picture[0]['gallery_name'], uri('acp/gallery/acp_edit_gallery/id_' . $picture[0]['gallery_id']));
	$breadcrumb->assign(lang('gallery', 'edit_picture'));

	if (isset($_POST['submit'])) {
		include 'modules/gallery/entry.php';
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$picture[0]['description'] = $db->escape($picture[0]['description'], 3);

		$galleries = $db->select('id, start, name', 'gallery', 0, 'start DESC');
		$c_galleries = count($galleries);

		for ($i = 0; $i < $c_galleries; $i++) {
			$galleries[$i]['selected'] = select_entry('gallery', $galleries[$i]['id'], $picture[0]['gallery_id']);
			$galleries[$i]['date'] = date_aligned(1, $galleries[$i]['start']);
			$galleries[$i]['name'] = $galleries[$i]['name'];
		}
		$tpl->assign('galleries', $galleries);

		$tpl->assign('form', isset($form) ? $form : $picture[0]);

		$content = $tpl->fetch('gallery/acp_edit_picture.html');
	}
} else {
	redirect('errors/404');
}
?>