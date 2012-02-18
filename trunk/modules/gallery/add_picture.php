<?php
/**
 * Gallery
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit();

if (validate::isNumber($uri->id) === true && $db->countRows('*', 'gallery', 'id = \'' . $uri->id . '\'') == 1) {
	require_once MODULES_DIR . 'gallery/functions.php';

	$gallery = $db->select('name', 'gallery', 'id = \'' . $uri->id . '\'');

	$breadcrumb->assign($gallery[0]['name'], $uri->route('acp/gallery/edit_gallery/id_' . $uri->id))
			   ->assign($lang->t('gallery', 'add_picture'));

	$settings = config::getModuleSettings('gallery');

	if (isset($_POST['form']) === true) {
		$file['tmp_name'] = $_FILES['file']['tmp_name'];
		$file['name'] = $_FILES['file']['name'];
		$file['size'] = $_FILES['file']['size'];
		$form = $_POST['form'];

		if (empty($file['tmp_name']))
			$errors['file'] = $lang->t('gallery', 'no_picture_selected');
		if (!empty($file['tmp_name']) &&
			(validate::isPicture($file['tmp_name'], $settings['maxwidth'], $settings['maxheight'], $settings['filesize']) === false ||
			$_FILES['file']['error'] !== UPLOAD_ERR_OK))
			$errors['file'] = $lang->t('gallery', 'invalid_image_selected');

		if (isset($errors) === true) {
			$tpl->assign('error_msg', errorBox($errors));
		} elseif (validate::formToken() === false) {
			view::setContent(errorBox($lang->t('common', 'form_already_submitted')));
		} else {
			$result = moveFile($file['tmp_name'], $file['name'], 'gallery');
			$picNum = $db->select('MAX(pic) AS pic', 'gallery_pictures', 'gallery_id = \'' . $uri->id . '\'');

			$insert_values = array(
				'id' => '',
				'pic' => !empty($picNum) && !is_null($picNum[0]['pic']) ? $picNum[0]['pic'] + 1 : 1,
				'gallery_id' => $uri->id,
				'file' => $result['name'],
				'description' => $db->escape($form['description'], 2),
				'comments' => $settings['comments'] == 1 && isset($form['comments']) && $form['comments'] == 1 ? 1 : 0,
			);

			$bool = $db->insert('gallery_pictures', $insert_values);
			$bool2 = generatePictureAlias($db->link->lastInsertId());
			setGalleryCache($uri->id);

			$session->unsetFormToken();

			setRedirectMessage($bool !== false && $bool2 !== false ? $lang->t('common', 'create_success') : $lang->t('common', 'create_error'), 'acp/gallery/edit_gallery/id_' . $uri->id);
		}
	}
	if (isset($_POST['form']) === false || isset($errors) === true && is_array($errors) === true) {
		$galleries = $db->select('id, start, name', 'gallery', 0, 'start DESC');
		$c_galleries = count($galleries);

		if ($settings['overlay'] == 0 && $settings['comments'] == 1 && modules::check('comments', 'functions') === true) {
			$options = array();
			$options[0]['name'] = 'comments';
			$options[0]['checked'] = selectEntry('comments', '1', '0', 'checked');
			$options[0]['lang'] = $lang->t('common', 'allow_comments');
			$tpl->assign('options', $options);
		}

		for ($i = 0; $i < $c_galleries; ++$i) {
			$galleries[$i]['selected'] = selectEntry('gallery', $galleries[$i]['id'], $uri->id);
			$galleries[$i]['date'] = $date->format($galleries[$i]['start']);
			$galleries[$i]['name'] = $db->escape($galleries[$i]['name'], 3);
		}

		$tpl->assign('galleries', $galleries);
		$tpl->assign('form', isset($form) ? $form : array('description' => ''));

		$session->generateFormToken();

		view::setContent(view::fetchTemplate('gallery/add_picture.tpl'));
	}
} else {
	$uri->redirect('errors/404');
}
