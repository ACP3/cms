<?php
/**
 * Gallery
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (ACP3_Validate::isNumber($uri->id) === true && $db->countRows('*', 'gallery_pictures', 'id = \'' . $uri->id . '\'') == 1) {
	require_once MODULES_DIR . 'gallery/functions.php';

	$picture = $db->select('p.gallery_id, p.file, p.description, p.comments, g.name AS gallery_name', 'gallery_pictures AS p, {pre}gallery AS g', 'p.id = \'' . $uri->id . '\' AND p.gallery_id = g.id');
	$picture[0]['description'] = $db->escape($picture[0]['description'], 3);
	$picture[0]['gallery_name'] = $db->escape($picture[0]['gallery_name'], 3);
	$picture[0]['description'] = $db->escape($picture[0]['description'], 3);

	$breadcrumb->append($picture[0]['gallery_name'], $uri->route('acp/gallery/edit_gallery/id_' . $picture[0]['gallery_id']))
			   ->append($lang->t('gallery', 'edit_picture'));

	$settings = ACP3_Config::getModuleSettings('gallery');

	if (isset($_POST['submit']) === true) {
		if (!empty($_FILES['file']['tmp_name']) && $_FILES['file']['size'] > '0') {
			$file['tmp_name'] = $_FILES['file']['tmp_name'];
			$file['name'] = $_FILES['file']['name'];
			$file['size'] = $_FILES['file']['size'];
		}

		if (!empty($file['tmp_name']) &&
			(ACP3_Validate::isPicture($file['tmp_name'], $settings['maxwidth'], $settings['maxheight'], $settings['filesize']) === false ||
			$_FILES['file']['error'] !== UPLOAD_ERR_OK))
			$errors['file'] = $lang->t('gallery', 'invalid_image_selected');

		if (isset($errors) === true) {
			$tpl->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
		} else {
			$new_file_sql = null;
			if (isset($file) && is_array($file)) {
				$result = moveFile($file['tmp_name'], $file['name'], 'gallery');
				$new_file_sql['file'] = $result['name'];
			}

			$update_values = array(
				'description' => $db->escape($_POST['description'], 2),
				'comments' => $settings['comments'] == 1 && isset($_POST['comments']) && $_POST['comments'] == 1 ? 1 : 0,
			);
			if (is_array($new_file_sql) === true) {
				$old_file = $db->select('file', 'gallery_pictures', 'id = \'' . $uri->id . '\'');
				removePicture($old_file[0]['file']);

				$update_values = array_merge($update_values, $new_file_sql);
			}

			$bool = $db->update('gallery_pictures', $update_values, 'id = \'' . $uri->id . '\'');
			setGalleryCache($picture[0]['gallery_id']);

			$session->unsetFormToken();

			setRedirectMessage($bool !== false ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), 'acp/gallery/edit_gallery/id_' . $picture[0]['gallery_id']);
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		if ($settings['overlay'] == 0 && $settings['comments'] == 1 && ACP3_Modules::check('comments', 'functions') === true) {
			$options = array();
			$options[0]['name'] = 'comments';
			$options[0]['checked'] = selectEntry('comments', '1', $picture[0]['comments'], 'checked');
			$options[0]['lang'] = $lang->t('common', 'allow_comments');
			$tpl->assign('options', $options);
		}

		$tpl->assign('form', isset($_POST['submit']) ? $_POST : $picture[0]);

		$session->generateFormToken();

		ACP3_View::setContent(ACP3_View::fetchTemplate('gallery/edit_picture.tpl'));
	}
} else {
	$uri->redirect('errors/404');
}
