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

$settings = ACP3_Config::getSettings('gallery');
$comments_active = ACP3_Modules::isActive('comments');

if (isset($_POST['submit']) === true) {
	if (empty($_POST['dateformat']) || ($_POST['dateformat'] !== 'long' && $_POST['dateformat'] !== 'short'))
		$errors['dateformat'] = ACP3_CMS::$lang->t('system', 'select_date_format');
	if (ACP3_Validate::isNumber($_POST['sidebar']) === false)
		$errors['sidebar'] = ACP3_CMS::$lang->t('system', 'select_sidebar_entries');
	if (!isset($_POST['overlay']) || $_POST['overlay'] != 1 && $_POST['overlay'] != 0)
		$errors[] = ACP3_CMS::$lang->t('gallery', 'select_use_overlay');
	if ($comments_active === true && (!isset($_POST['comments']) || $_POST['comments'] != 1 && $_POST['comments'] != 0))
		$errors[] = ACP3_CMS::$lang->t('gallery', 'select_allow_comments');
	if (ACP3_Validate::isNumber($_POST['thumbwidth']) === false || ACP3_Validate::isNumber($_POST['width']) === false || ACP3_Validate::isNumber($_POST['maxwidth']) === false)
		$errors[] = ACP3_CMS::$lang->t('gallery', 'invalid_image_width_entered');
	if (ACP3_Validate::isNumber($_POST['thumbheight']) === false || ACP3_Validate::isNumber($_POST['height']) === false || ACP3_Validate::isNumber($_POST['maxheight']) === false)
		$errors[] = ACP3_CMS::$lang->t('gallery', 'invalid_image_height_entered');
	if (ACP3_Validate::isNumber($_POST['filesize']) === false)
		$errors['filesize'] = ACP3_CMS::$lang->t('gallery', 'invalid_image_filesize_entered');

	if (isset($errors) === true) {
		ACP3_CMS::$view->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
	} else {
		$data = array(
			'width' => (int) $_POST['width'],
			'height' => (int) $_POST['height'],
			'thumbwidth' => (int) $_POST['thumbwidth'],
			'thumbheight' => (int) $_POST['thumbheight'],
			'maxwidth' => (int) $_POST['maxwidth'],
			'maxheight' => (int) $_POST['maxheight'],
			'filesize' => (int) $_POST['filesize'],
			'overlay' => $_POST['overlay'],
			'dateformat' => str_encode($_POST['dateformat']),
			'sidebar' => (int) $_POST['sidebar'],
		);
		if ($comments_active === true)
			$data['comments'] = $_POST['comments'];

		$bool = ACP3_Config::setSettings('gallery', $data);

		// Falls sich die anzuzeigenden Bildgrößen geändert haben, die gecacheten Bilder löschen
		if ($_POST['thumbwidth'] !== $settings['thumbwidth'] || $_POST['thumbheight'] !== $settings['thumbheight'] ||
			$_POST['width'] !== $settings['width'] || $_POST['height'] !== $settings['height']) {
			ACP3_Cache::purge('images', 'gallery');
			ACP3_Cache::purge('sql', 'gallery');
		}

		ACP3_CMS::$session->unsetFormToken();

		setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/gallery');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	if ($comments_active === true) {
		$comments = array();
		$comments[0]['value'] = '1';
		$comments[0]['checked'] = selectEntry('comments', '1', $settings['comments'], 'checked');
		$comments[0]['lang'] = ACP3_CMS::$lang->t('system', 'yes');
		$comments[1]['value'] = '0';
		$comments[1]['checked'] = selectEntry('comments', '0', $settings['comments'], 'checked');
		$comments[1]['lang'] = ACP3_CMS::$lang->t('system', 'no');
		ACP3_CMS::$view->assign('comments', $comments);
	}

	$overlay = array();
	$overlay[0]['value'] = '1';
	$overlay[0]['checked'] = selectEntry('overlay', '1', $settings['overlay'], 'checked');
	$overlay[0]['lang'] = ACP3_CMS::$lang->t('system', 'yes');
	$overlay[1]['value'] = '0';
	$overlay[1]['checked'] = selectEntry('overlay', '0', $settings['overlay'], 'checked');
	$overlay[1]['lang'] = ACP3_CMS::$lang->t('system', 'no');
	ACP3_CMS::$view->assign('overlay', $overlay);

	ACP3_CMS::$view->assign('dateformat', ACP3_CMS::$date->dateformatDropdown($settings['dateformat']));

	ACP3_CMS::$view->assign('sidebar_entries', recordsPerPage((int) $settings['sidebar'], 1, 10));

	ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : $settings);

	ACP3_CMS::$session->generateFormToken();
}
