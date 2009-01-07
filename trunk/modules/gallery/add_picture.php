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

if (validate::isNumber($uri->id) && $db->select('COUNT(id)', 'gallery', 'id = \'' . $uri->id . '\'', 0, 0, 0, 1) == '1') {
	$gallery = $db->select('name', 'gallery', 'id = \'' . $uri->id . '\'');

	breadcrumb::assign($lang->t('common', 'acp'), uri('acp'));
	breadcrumb::assign($lang->t('gallery', 'gallery'), uri('acp/gallery'));
	breadcrumb::assign($gallery[0]['name'], uri('acp/gallery/edit_gallery/id_' . $uri->id));
	breadcrumb::assign($lang->t('gallery', 'add_picture'));

	if (isset($_POST['submit'])) {
		$file['tmp_name'] = $_FILES['file']['tmp_name'];
		$file['name'] = $_FILES['file']['name'];
		$file['size'] = $_FILES['file']['size'];
		$form = $_POST['form'];
		$settings = config::output('gallery');

		if (empty($file['tmp_name']))
			$errors[] = $lang->t('gallery', 'no_picture_selected');
		if (!empty($file['tmp_name']) && !validate::isPicture($file['tmp_name'], $settings['maxwidth'], $settings['maxheight'], $settings['filesize']))
			$errors[] = $lang->t('gallery', 'invalid_image_selected');

		if (isset($errors)) {
			$tpl->assign('error_msg', comboBox($errors));
		} else {
			$result = moveFile($file['tmp_name'], $file['name'], 'gallery');
			$picNum = $db->select('pic', 'gallery_pictures', 'gallery_id = \'' . $uri->id . '\'', 'pic DESC', 1);

			$insert_values = array(
				'id' => '',
				'pic' => !empty($picNum) ? $picNum[0]['pic'] + 1 : 1,
				'gallery_id' => $uri->id,
				'file' => $result['name'],
				'description' => $db->escape($form['description'], 2)
			);

			$bool = $db->insert('gallery_pictures', $insert_values);
			setGalleryCache($uri->id);

			$content = comboBox($bool ? $lang->t('common', 'create_success') : $lang->t('common', 'create_error'), uri('acp/gallery/edit_gallery/id_' . $uri->id));
		}
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$galleries = $db->select('id, start, name', 'gallery', 0, 'start DESC');
		$c_galleries = count($galleries);

		for ($i = 0; $i < $c_galleries; ++$i) {
			$galleries[$i]['selected'] = selectEntry('gallery', $galleries[$i]['id'], $uri->id);
			$galleries[$i]['date'] = $date->format($galleries[$i]['start']);
			$galleries[$i]['name'] = $db->escape($galleries[$i]['name'], 3);
		}

		$tpl->assign('galleries', $galleries);
		$tpl->assign('form', isset($form) ? $form : array('description' => ''));

		$content = $tpl->fetch('gallery/add_picture.html');
	}
} else {
	redirect('errors/404');
}
?>