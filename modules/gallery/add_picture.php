<?php
/**
 * Gallery
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit();

if (validate::isNumber($modules->id) && $db->select('id', 'gallery', 'id = \'' . $modules->id . '\'', 0, 0, 0, 1) == '1') {
	$pic = validate::isNumber($modules->pic) ? $modules->pic : 1;
	$gallery = $db->select('name', 'gallery', 'id = \'' . $modules->id . '\'');

	$breadcrumb->assign(lang('common', 'acp'), uri('acp'));
	$breadcrumb->assign(lang('gallery', 'gallery'), uri('acp/gallery'));
	$breadcrumb->assign($gallery[0]['name'], uri('acp/gallery/edit_gallery/id_' . $modules->id));
	$breadcrumb->assign(lang('gallery', 'add_picture'));

	if (isset($_POST['submit'])) {
		$file['tmp_name'] = $_FILES['file']['tmp_name'];
		$file['name'] = $_FILES['file']['name'];
		$file['size'] = $_FILES['file']['size'];
		$form = $_POST['form'];
		$settings = config::output('gallery');

		if (!validate::isNumber($form['gallery']) || $db->select('id', 'gallery', 'id = \'' . $form['gallery'] . '\'', 0, 0, 0, 1) != '1')
			$errors[] = lang('gallery', 'no_gallery_selected');
		if (!validate::isNumber($form['pic']))
			$errors[] = lang('gallery', 'type_in_picture_number');
		if (empty($file['tmp_name']) || empty($file['size']))
			$errors[] = lang('gallery', 'no_picture_selected');
		if (!empty($file['tmp_name']) && !empty($file['size']) && !validate::isPicture($file['tmp_name'], $settings['width'], $settings['height'], $settings['filesize']))
			$errors[] = lang('gallery', 'invalid_image_selected');

		if (isset($errors)) {
			$tpl->assign('error_msg', comboBox($errors));
		} else {
			$result = moveFile($file['tmp_name'], $file['name'], 'gallery');

			$insert_values = array('id' => '', 'pic' => $form['pic'], 'gallery_id' => $form['gallery'], 'file' => $result['name'], 'description' => $db->escape($form['description'], 2));

			$bool = $db->insert('galpics', $insert_values);

			cache::create('gallery_pics_id_' . $form['gallery'], $db->select('id', 'galpics', 'gallery_id = \'' . $modules->id . '\'', 'id ASC'));

			$content = comboBox($bool ? lang('gallery', 'add_picture_success') : lang('gallery', 'add_picture_error'), uri('acp/gallery/add_picture/id_' . $form['gallery'] . '/pic_' . ($pic + 1)));
		}
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$galleries = $db->select('id, start, name', 'gallery', 0, 'start DESC');
		$c_galleries = count($galleries);

		for ($i = 0; $i < $c_galleries; ++$i) {
			$galleries[$i]['selected'] = selectEntry('gallery', $galleries[$i]['id'], $modules->id);
			$galleries[$i]['date'] = dateAligned(1, $galleries[$i]['start']);
			$galleries[$i]['name'] = $db->escape($galleries[$i]['name'], 3);
		}

		$tpl->assign('galleries', $galleries);
		$form['pic'] = isset($form['pic']) ? $form['pic'] : $pic;
		$tpl->assign('form', $form);

		$content = $tpl->fetch('gallery/add_picture.html');
	}
} else {
	redirect('errors/404');
}
?>