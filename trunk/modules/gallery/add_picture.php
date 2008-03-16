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

$pic = isset($modules->gen['pic']) && $validate->is_number($modules->gen['pic']) ? $modules->gen['pic'] : 1;

if (!empty($modules->id)) {
	$gallery = $db->select('name', 'gallery', 'id = \'' . $modules->id . '\'');

	$breadcrumb->assign(lang('gallery', 'gallery'), uri('acp/gallery'));
	$breadcrumb->assign($gallery[0]['name'], uri('acp/gallery/edit_gallery/id_' . $modules->id));
	$breadcrumb->assign(lang('gallery', 'add_picture'));
}

if (isset($_POST['submit'])) {
	$file['tmp_name'] = $_FILES['file']['tmp_name'];
	$file['name'] = $_FILES['file']['name'];
	$file['size'] = $_FILES['file']['size'];
	$form = $_POST['form'];

	if (!$validate->is_number($form['gallery']) || $db->select('id', 'gallery', 'id = \'' . $form['gallery'] . '\'', 0, 0, 0, 1) != '1')
	$errors[] = lang('gallery', 'no_gallery_selected');
	if (!$validate->is_number($form['pic']))
	$errors[] = lang('gallery', 'type_in_picture_number');
	if (empty($file['tmp_name']) || empty($file['size']))
	$errors[] = lang('gallery', 'no_picture_selected');
	if (!empty($file['tmp_name']) && !empty($file['size']) && !$validate->is_picture($file['tmp_name']))
	$errors[] = lang('gallery', 'only_png_jpg_gif_allowed');

	if (isset($errors)) {
		$tpl->assign('error_msg', combo_box($errors));
	} else {
		$result = move_file($file['tmp_name'], $file['name'], 'gallery');

		$insert_values = array(
		'id' => '',
		'pic' => $form['pic'],
		'gallery_id' => $form['gallery'],
		'file' => $result['name'],
		'description' => $db->escape($form['description'], 2),
		);

		$bool = $db->insert('galpics', $insert_values);

		$cache->create('gallery_pics_id_' . $form['gallery'], $db->select('id', 'galpics', 'gallery_id = \'' . $modules->id . '\'', 'id ASC'));

		$content = combo_box($bool ? lang('gallery', 'add_picture_success') : lang('gallery', 'add_picture_error'), uri('acp/gallery/add_picture/id_' . $form['gallery'] . '/pic_' . ($pic + 1)));
	}
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

	$content = $tpl->fetch('gallery/add_picture.html');
}
?>