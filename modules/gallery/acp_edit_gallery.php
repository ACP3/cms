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

if (!empty($modules->id) && $db->select('id', 'gallery', 'id = \'' . $modules->id . '\'', 0, 0, 0, 1) == '1') {
	$gallery = $db->select('start, end, name', 'gallery', 'id = \'' . $modules->id . '\'');

	$breadcrumb->assign(lang('gallery', 'gallery'), uri('acp/gallery'));
	$breadcrumb->assign($gallery[0]['name']);

	if (isset($_POST['entries']) || isset($modules->gen['entries']) || isset($_POST['submit'])) {
		include 'modules/gallery/entry.php';
	}
	if (!isset($_POST['entries']) && !isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$tpl->assign('gallery_id', $modules->id);

		// Datumsauswahl
		$tpl->assign('start_date', publication_period('start', $gallery[0]['start']));
		$tpl->assign('end_date', publication_period('end', $gallery[0]['end']));

		$tpl->assign('form', isset($form) ? $form : $gallery[0]);

		$pictures = $db->select('id, pic, file, description', 'galpics', 'gallery_id = \'' . $modules->id . '\'', 'pic ASC', POS, CONFIG_ENTRIES);
		$c_pictures = count($pictures);

		if ($c_pictures > 0) {
			$tpl->assign('pagination', pagination($db->select('id', 'galpics', 'gallery_id = \'' . $modules->id . '\'', 0, 0, 0, 1)));
			for ($i = 0; $i < $c_pictures; $i++) {
				$pictures[$i]['description'] = $db->escape($pictures[$i]['description'], 3);
			}
			$tpl->assign('pictures', $pictures);
		}

		$content = $tpl->fetch('gallery/acp_edit_gallery.html');
	}
} else {
	redirect('errors/404');
}
?>