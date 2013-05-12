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

if (ACP3\Core\Validate::isNumber(ACP3\CMS::$injector['URI']->id) === true &&
	ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery_pictures WHERE id = ?', array(ACP3\CMS::$injector['URI']->id)) == 1) {
	require_once MODULES_DIR . 'gallery/functions.php';

	$picture = ACP3\CMS::$injector['Db']->fetchAssoc('SELECT p.gallery_id, p.file, p.description, p.comments, g.title AS gallery_title FROM ' . DB_PRE . 'gallery_pictures AS p, ' . DB_PRE . 'gallery AS g WHERE p.id = ? AND p.gallery_id = g.id', array(ACP3\CMS::$injector['URI']->id));

	ACP3\CMS::$injector['Breadcrumb']
	->append($picture['gallery_title'], ACP3\CMS::$injector['URI']->route('acp/gallery/edit/id_' . $picture['gallery_id']))
	->append(ACP3\CMS::$injector['Lang']->t('gallery', 'acp_edit_picture'));

	$settings = ACP3\Core\Config::getSettings('gallery');

	if (isset($_POST['submit']) === true) {
		if (!empty($_FILES['file']['tmp_name']) && $_FILES['file']['size'] > '0') {
			$file['tmp_name'] = $_FILES['file']['tmp_name'];
			$file['name'] = $_FILES['file']['name'];
			$file['size'] = $_FILES['file']['size'];
		}

		if (!empty($file['tmp_name']) &&
			(ACP3\Core\Validate::isPicture($file['tmp_name'], $settings['maxwidth'], $settings['maxheight'], $settings['filesize']) === false ||
			$_FILES['file']['error'] !== UPLOAD_ERR_OK))
			$errors['file'] = ACP3\CMS::$injector['Lang']->t('gallery', 'invalid_image_selected');

		if (isset($errors) === true) {
			ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
		} elseif (ACP3\Core\Validate::formToken() === false) {
			ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted')));
		} else {
			$new_file_sql = null;
			if (isset($file) && is_array($file)) {
				$result = moveFile($file['tmp_name'], $file['name'], 'gallery');
				$new_file_sql['file'] = $result['name'];
			}

			$update_values = array(
				'description' => ACP3\Core\Functions::str_encode($_POST['description'], true),
				'comments' => $settings['comments'] == 1 ? (isset($_POST['comments']) && $_POST['comments'] == 1 ? 1 : 0) : $settings['comments'],
			);
			if (is_array($new_file_sql) === true) {
				$old_file = ACP3\CMS::$injector['Db']->fetchColumn('SELECT file FROM ' . DB_PRE . 'gallery_pictures WHERE id = ?', array(ACP3\CMS::$injector['URI']->id));
				removePicture($old_file);

				$update_values = array_merge($update_values, $new_file_sql);
			}

			$bool = ACP3\CMS::$injector['Db']->update(DB_PRE . 'gallery_pictures', $update_values, array('id' => ACP3\CMS::$injector['URI']->id));
			setGalleryCache($picture['gallery_id']);

			ACP3\CMS::$injector['Session']->unsetFormToken();

			ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/gallery/edit/id_' . $picture['gallery_id']);
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		if ($settings['overlay'] == 0 && $settings['comments'] == 1 && ACP3\Core\Modules::check('comments', 'functions') === true) {
			$options = array();
			$options[0]['name'] = 'comments';
			$options[0]['checked'] = ACP3\Core\Functions::selectEntry('comments', '1', $picture['comments'], 'checked');
			$options[0]['lang'] = ACP3\CMS::$injector['Lang']->t('system', 'allow_comments');
			ACP3\CMS::$injector['View']->assign('options', $options);
		}

		ACP3\CMS::$injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $picture);
		ACP3\CMS::$injector['View']->assign('gallery_id', ACP3\CMS::$injector['URI']->id);

		ACP3\CMS::$injector['Session']->generateFormToken();
	}
} else {
	ACP3\CMS::$injector['URI']->redirect('errors/404');
}
