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

if (!empty($modules->id) && $db->select('id', 'galpics', 'id = \'' . $modules->id . '\'', 0, 0, 0, 1) == '1') {
	if (isset($_POST['submit'])) {
		include 'modules/gallery/entry.php';
	}
	if (!isset($_POST['submit']) || isset($error_msg)) {
		$tpl->assign('error_msg', isset($error_msg) ? $error_msg : '');

		$picture = $db->select('pic, gallery, file, description', 'galpics', 'id = \'' . $modules->id . '\'');
		$picture[0]['description'] = $db->escape($picture[0]['description'], 3);

		$galleries = $db->select('id, start, name', 'gallery', 0, 'start DESC');
		$c_galleries = count($galleries);

		for ($i = 0; $i < $c_galleries; $i++) {
			$galleries[$i]['selected'] = select_entry('gallery', $galleries[$i]['id'], $picture[0]['gallery']);
			$galleries[$i]['date'] = date_aligned(1, $galleries[$i]['start']);
			$galleries[$i]['name'] = $galleries[$i]['name'];
		}
		$tpl->assign('form', isset($form) ? $form : $picture[0]);

		$content = $tpl->fetch('gallery/edit_picture.html');
	}
} else {
	redirect('errors/404');
}
?>