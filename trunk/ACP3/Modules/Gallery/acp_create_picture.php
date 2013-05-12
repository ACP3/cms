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

if (ACP3\Core\Validate::isNumber(ACP3\CMS::$injector['URI']->id) === true &&
	ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'gallery WHERE id = ?', array(ACP3\CMS::$injector['URI']->id)) == 1) {
	require_once MODULES_DIR . 'gallery/functions.php';

	$gallery = ACP3\CMS::$injector['Db']->fetchColumn('SELECT title FROM ' . DB_PRE . 'gallery WHERE id = ?', array(ACP3\CMS::$injector['URI']->id));

	ACP3\CMS::$injector['Breadcrumb']
	->append($gallery, ACP3\CMS::$injector['URI']->route('acp/gallery/edit/id_' . ACP3\CMS::$injector['URI']->id))
	->append(ACP3\CMS::$injector['Lang']->t('gallery', 'acp_create_picture'));

	$settings = ACP3\Core\Config::getSettings('gallery');

	if (isset($_POST['submit']) === true) {
		$file['tmp_name'] = $_FILES['file']['tmp_name'];
		$file['name'] = $_FILES['file']['name'];
		$file['size'] = $_FILES['file']['size'];

		if (empty($file['tmp_name']))
			$errors['file'] = ACP3\CMS::$injector['Lang']->t('gallery', 'no_picture_selected');
		if (!empty($file['tmp_name']) &&
			(ACP3\Core\Validate::isPicture($file['tmp_name'], $settings['maxwidth'], $settings['maxheight'], $settings['filesize']) === false ||
			$_FILES['file']['error'] !== UPLOAD_ERR_OK))
			$errors['file'] = ACP3\CMS::$injector['Lang']->t('gallery', 'invalid_image_selected');

		if (isset($errors) === true) {
			ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
		} elseif (ACP3\Core\Validate::formToken() === false) {
			ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted')));
		} else {
			$result = Core\Functions::moveFile($file['tmp_name'], $file['name'], 'gallery');
			$picNum = ACP3\CMS::$injector['Db']->fetchColumn('SELECT MAX(pic) FROM ' . DB_PRE . 'gallery_pictures WHERE gallery_id = ?', array(ACP3\CMS::$injector['URI']->id));

			$insert_values = array(
				'id' => '',
				'pic' => !is_null($picNum) ? $picNum + 1 : 1,
				'gallery_id' => ACP3\CMS::$injector['URI']->id,
				'file' => $result['name'],
				'description' => ACP3\Core\Functions::str_encode($_POST['description'], true),
				'comments' => $settings['comments'] == 1 ? (isset($_POST['comments']) && $_POST['comments'] == 1 ? 1 : 0) : $settings['comments'],
			);

			$bool = ACP3\CMS::$injector['Db']->insert(DB_PRE . 'gallery_pictures', $insert_values);
			$bool2 = generatePictureAlias(ACP3\CMS::$injector['Db']->lastInsertId());
			setGalleryCache(ACP3\CMS::$injector['URI']->id);

			ACP3\CMS::$injector['Session']->unsetFormToken();

			ACP3\Core\Functions::setRedirectMessage($bool && $bool2, ACP3\CMS::$injector['Lang']->t('system', $bool !== false && $bool2 !== false ? 'create_success' : 'create_error'), 'acp/gallery/edit/id_' . ACP3\CMS::$injector['URI']->id);
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		if ($settings['overlay'] == 0 && $settings['comments'] == 1 && ACP3\Core\Modules::check('comments', 'functions') === true) {
			$options = array();
			$options[0]['name'] = 'comments';
			$options[0]['checked'] = ACP3\Core\Functions::selectEntry('comments', '1', '0', 'checked');
			$options[0]['lang'] = ACP3\CMS::$injector['Lang']->t('system', 'allow_comments');
			ACP3\CMS::$injector['View']->assign('options', $options);
		}

		$galleries = ACP3\CMS::$injector['Db']->fetchAll('SELECT id, start, title FROM ' . DB_PRE . 'gallery ORDER BY start DESC');
		$c_galleries = count($galleries);
		for ($i = 0; $i < $c_galleries; ++$i) {
			$galleries[$i]['selected'] = ACP3\Core\Functions::selectEntry('gallery', $galleries[$i]['id'], ACP3\CMS::$injector['URI']->id);
			$galleries[$i]['date'] = ACP3\CMS::$injector['Date']->format($galleries[$i]['start']);
		}

		ACP3\CMS::$injector['View']->assign('galleries', $galleries);
		ACP3\CMS::$injector['View']->assign('form', isset($_POST['submit']) ? $_POST : array('description' => ''));
		ACP3\CMS::$injector['View']->assign('gallery_id', ACP3\CMS::$injector['URI']->id);

		ACP3\CMS::$injector['Session']->generateFormToken();
	}
} else {
	ACP3\CMS::$injector['URI']->redirect('errors/404');
}
