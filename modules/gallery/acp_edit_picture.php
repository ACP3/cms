<?php
/**
 * Gallery
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true &&
	ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery_pictures WHERE id = ?', array(ACP3_CMS::$uri->id)) == 1) {
	require_once MODULES_DIR . 'gallery/functions.php';

	$picture = ACP3_CMS::$db2->fetchAssoc('SELECT p.gallery_id, p.file, p.description, p.comments, g.name AS gallery_name FROM ' . DB_PRE . 'gallery_pictures AS p, ' . DB_PRE . 'gallery AS g WHERE p.id = ? AND p.gallery_id = g.id', array(ACP3_CMS::$uri->id));

	ACP3_CMS::$breadcrumb
	->append($picture['gallery_name'], ACP3_CMS::$uri->route('acp/gallery/edit/id_' . $picture['gallery_id']))
	->append(ACP3_CMS::$lang->t('gallery', 'acp_edit_picture'));

	$settings = ACP3_Config::getSettings('gallery');

	if (isset($_POST['submit']) === true) {
		if (!empty($_FILES['file']['tmp_name']) && $_FILES['file']['size'] > '0') {
			$file['tmp_name'] = $_FILES['file']['tmp_name'];
			$file['name'] = $_FILES['file']['name'];
			$file['size'] = $_FILES['file']['size'];
		}

		if (!empty($file['tmp_name']) &&
			(ACP3_Validate::isPicture($file['tmp_name'], $settings['maxwidth'], $settings['maxheight'], $settings['filesize']) === false ||
			$_FILES['file']['error'] !== UPLOAD_ERR_OK))
			$errors['file'] = ACP3_CMS::$lang->t('gallery', 'invalid_image_selected');

		if (isset($errors) === true) {
			ACP3_CMS::$view->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
		} else {
			$new_file_sql = null;
			if (isset($file) && is_array($file)) {
				$result = moveFile($file['tmp_name'], $file['name'], 'gallery');
				$new_file_sql['file'] = $result['name'];
			}

			$update_values = array(
				'description' => str_encode($_POST['description'], true),
				'comments' => $settings['comments'] == 1 && isset($_POST['comments']) && $_POST['comments'] == 1 ? 1 : 0,
			);
			if (is_array($new_file_sql) === true) {
				$old_file = ACP3_CMS::$db2->fetchColumn('SELECT file FROM ' . DB_PRE . 'gallery_pictures WHERE id = ?', array(ACP3_CMS::$uri->id));
				removePicture($old_file);

				$update_values = array_merge($update_values, $new_file_sql);
			}

			$bool = ACP3_CMS::$db2->update(DB_PRE . 'gallery_pictures', $update_values, array('id' => ACP3_CMS::$uri->id));
			setGalleryCache($picture['gallery_id']);

			ACP3_CMS::$session->unsetFormToken();

			setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/gallery/edit/id_' . $picture['gallery_id']);
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		if ($settings['overlay'] == 0 && $settings['comments'] == 1 && ACP3_Modules::check('comments', 'functions') === true) {
			$options = array();
			$options[0]['name'] = 'comments';
			$options[0]['checked'] = selectEntry('comments', '1', $picture['comments'], 'checked');
			$options[0]['lang'] = ACP3_CMS::$lang->t('system', 'allow_comments');
			ACP3_CMS::$view->assign('options', $options);
		}

		ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : $picture);
		ACP3_CMS::$view->assign('gallery_id', ACP3_CMS::$uri->id);

		ACP3_CMS::$session->generateFormToken();

		ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('gallery/acp_edit_picture.tpl'));
	}
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}
