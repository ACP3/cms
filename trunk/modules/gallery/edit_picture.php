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

if (validate::isNumber($uri->id) && $db->countRows('*', 'gallery_pictures', 'id = \'' . $uri->id . '\'') == '1') {
	require_once MODULES_DIR . 'gallery/functions.php';

	$picture = $db->select('p.gallery_id, p.file, p.description, p.comments, g.name AS gallery_name', 'gallery_pictures AS p, ' . $db->prefix . 'gallery AS g', 'p.id = \'' . $uri->id . '\' AND p.gallery_id = g.id');
	$picture[0]['description'] = $db->escape($picture[0]['description'], 3);

	breadcrumb::assign($lang->t('common', 'acp'), $uri->route('acp'));
	breadcrumb::assign($lang->t('gallery', 'gallery'), $uri->route('acp/gallery'));
	breadcrumb::assign($picture[0]['gallery_name'], $uri->route('acp/gallery/edit_gallery/id_' . $picture[0]['gallery_id']));
	breadcrumb::assign($lang->t('gallery', 'edit_picture'));

	$settings = config::getModuleSettings('gallery');

	if (isset($_POST['form'])) {
		if (!empty($_FILES['file']['tmp_name']) && $_FILES['file']['size'] > '0') {
			$file['tmp_name'] = $_FILES['file']['tmp_name'];
			$file['name'] = $_FILES['file']['name'];
			$file['size'] = $_FILES['file']['size'];
		}
		$form = $_POST['form'];

		if (!empty($file['tmp_name']) &&
			(!validate::isPicture($file['tmp_name'], $settings['maxwidth'], $settings['maxheight'], $settings['filesize']) ||
			$_FILES['file']['error'] !== UPLOAD_ERR_OK))
			$errors[] = $lang->t('gallery', 'invalid_image_selected');

		if (isset($errors)) {
			$tpl->assign('error_msg', comboBox($errors));
		} else {
			$new_file_sql = null;
			if (isset($file) && is_array($file)) {
				$result = moveFile($file['tmp_name'], $file['name'], 'gallery');
				$new_file_sql['file'] = $result['name'];
			}

			$update_values = array(
				'description' => $db->escape($form['description'], 2),
				'comments' => $settings['comments'] == 1 && isset($form['comments']) && $form['comments'] == 1 ? 1 : 0,
			);
			if (is_array($new_file_sql)) {
				$old_file = $db->select('file', 'gallery_pictures', 'id = \'' . $uri->id . '\'');
				removeFile('gallery', $old_file[0]['file']);

				$update_values = array_merge($update_values, $new_file_sql);
			}

			$bool = $db->update('gallery_pictures', $update_values, 'id = \'' . $uri->id . '\'');
			setGalleryCache($picture[0]['gallery_id']);

			$content = comboBox($bool ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), $uri->route('acp/gallery/edit_gallery/id_' . $picture[0]['gallery_id']));
		}
	}
	if (!isset($_POST['form']) || isset($errors) && is_array($errors)) {
		if ($settings['colorbox'] == 0 && $settings['comments'] == 1 && modules::check('comments', 'functions') == 1) {
			$options = array();
			$options[0]['name'] = 'comments';
			$options[0]['checked'] = selectEntry('comments', '1', $picture[0]['comments'], 'checked');
			$options[0]['lang'] = $lang->t('common', 'allow_comments');
			$tpl->assign('options', $options);
		}

		$tpl->assign('form', isset($form) ? $form : $picture[0]);

		$content = modules::fetchTemplate('gallery/edit_picture.html');
	}
} else {
	$uri->redirect('errors/404');
}
