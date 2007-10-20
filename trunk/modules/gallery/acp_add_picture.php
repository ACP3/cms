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

$pic = isset($modules->gen['pic']) && $validate->is_number($modules->gen['pic']) ? $modules->gen['pic'] : 1;

if (!empty($modules->id)) {
	$gallery = $db->select('name', 'gallery', 'id = \'' . $modules->id . '\'');

	$breadcrumb->assign(lang('gallery', 'gallery'), uri('acp/gallery'));
	$breadcrumb->assign($gallery[0]['name'], uri('acp/gallery/acp_edit_gallery/id_' . $modules->id));
	$breadcrumb->assign(lang('gallery', 'add_picture'));
	unset($gallery);
}

if (isset($_POST['submit'])) {
	include 'modules/gallery/entry.php';
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	$galleries = $db->select('id, start, name', 'gallery', 0, 'start DESC');
	$c_galleries = count($galleries);

	for ($i = 0; $i < $c_galleries; $i++) {
		$galleries[$i]['selected'] = select_entry('gallery', $galleries[$i]['id'], $modules->id);
		$galleries[$i]['date'] = date_aligned(1, $galleries[$i]['start']);
		$galleries[$i]['name'] = $galleries[$i]['name'];
	}

	$tpl->assign('galleries', $galleries);
	$form['pic'] = isset($form['pic']) ? $form['pic'] : $pic;
	$tpl->assign('form', $form);

	$content = $tpl->fetch('gallery/acp_add_picture.html');
}
?>