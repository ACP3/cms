<?php
/**
 * Gallery
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit();

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true &&
	ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery WHERE id = ?', array(ACP3_CMS::$uri->id)) == 1) {
	require_once MODULES_DIR . 'gallery/functions.php';

	$gallery = ACP3_CMS::$db2->fetchColumn('SELECT title FROM ' . DB_PRE . 'gallery WHERE id = ?', array(ACP3_CMS::$uri->id));

	ACP3_CMS::$breadcrumb
	->append($gallery, ACP3_CMS::$uri->route('acp/gallery/edit/id_' . ACP3_CMS::$uri->id))
	->append(ACP3_CMS::$lang->t('gallery', 'acp_create_picture'));

	$settings = ACP3_Config::getSettings('gallery');

	if (isset($_POST['submit']) === true) {
		$file['tmp_name'] = $_FILES['file']['tmp_name'];
		$file['name'] = $_FILES['file']['name'];
		$file['size'] = $_FILES['file']['size'];

		if (empty($file['tmp_name']))
			$errors['file'] = ACP3_CMS::$lang->t('gallery', 'no_picture_selected');
		if (!empty($file['tmp_name']) &&
			(ACP3_Validate::isPicture($file['tmp_name'], $settings['maxwidth'], $settings['maxheight'], $settings['filesize']) === false ||
			$_FILES['file']['error'] !== UPLOAD_ERR_OK))
			$errors['file'] = ACP3_CMS::$lang->t('gallery', 'invalid_image_selected');

		if (isset($errors) === true) {
			ACP3_CMS::$view->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
		} else {
			$result = moveFile($file['tmp_name'], $file['name'], 'gallery');
			$picNum = ACP3_CMS::$db2->fetchColumn('SELECT MAX(pic) FROM ' . DB_PRE . 'gallery_pictures WHERE gallery_id = ?', array(ACP3_CMS::$uri->id));

			$insert_values = array(
				'id' => '',
				'pic' => !is_null($picNum) ? $picNum + 1 : 1,
				'gallery_id' => ACP3_CMS::$uri->id,
				'file' => $result['name'],
				'description' => str_encode($_POST['description'], true),
				'comments' => $settings['comments'] == 1 ? (isset($_POST['comments']) && $_POST['comments'] == 1 ? 1 : 0) : $settings['comments'],
			);

			$bool = ACP3_CMS::$db2->insert(DB_PRE . 'gallery_pictures', $insert_values);
			$bool2 = generatePictureAlias(ACP3_CMS::$db2->lastInsertId());
			setGalleryCache(ACP3_CMS::$uri->id);

			ACP3_CMS::$session->unsetFormToken();

			setRedirectMessage($bool && $bool2, ACP3_CMS::$lang->t('system', $bool !== false && $bool2 !== false ? 'create_success' : 'create_error'), 'acp/gallery/edit/id_' . ACP3_CMS::$uri->id);
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		if ($settings['overlay'] == 0 && $settings['comments'] == 1 && ACP3_Modules::check('comments', 'functions') === true) {
			$options = array();
			$options[0]['name'] = 'comments';
			$options[0]['checked'] = selectEntry('comments', '1', '0', 'checked');
			$options[0]['lang'] = ACP3_CMS::$lang->t('system', 'allow_comments');
			ACP3_CMS::$view->assign('options', $options);
		}

		$galleries = ACP3_CMS::$db2->fetchAll('SELECT id, start, title FROM ' . DB_PRE . 'gallery ORDER BY start DESC');
		$c_galleries = count($galleries);
		for ($i = 0; $i < $c_galleries; ++$i) {
			$galleries[$i]['selected'] = selectEntry('gallery', $galleries[$i]['id'], ACP3_CMS::$uri->id);
			$galleries[$i]['date'] = ACP3_CMS::$date->format($galleries[$i]['start']);
		}

		ACP3_CMS::$view->assign('galleries', $galleries);
		ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : array('description' => ''));
		ACP3_CMS::$view->assign('gallery_id', ACP3_CMS::$uri->id);

		ACP3_CMS::$session->generateFormToken();
	}
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}
