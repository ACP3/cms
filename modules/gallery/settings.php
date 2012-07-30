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

$settings = ACP3_Config::getModuleSettings('gallery');
$comments_active = ACP3_Modules::isActive('comments');

if (isset($_POST['submit']) === true) {
	if (empty($_POST['dateformat']) || ($_POST['dateformat'] !== 'long' && $_POST['dateformat'] !== 'short'))
		$errors['dateformat'] = $lang->t('common', 'select_date_format');
	if (ACP3_Validate::isNumber($_POST['sidebar']) === false)
		$errors['sidebar'] = $lang->t('common', 'select_sidebar_entries');
	if (!isset($_POST['overlay']) || $_POST['overlay'] != 1 && $_POST['overlay'] != 0)
		$errors[] = $lang->t('gallery', 'select_use_overlay');
	if ($comments_active === true && (!isset($_POST['comments']) || $_POST['comments'] != 1 && $_POST['comments'] != 0))
		$errors[] = $lang->t('gallery', 'select_allow_comments');
	if (ACP3_Validate::isNumber($_POST['thumbwidth']) === false || ACP3_Validate::isNumber($_POST['width']) === false || ACP3_Validate::isNumber($_POST['maxwidth']) === false)
		$errors[] = $lang->t('gallery', 'invalid_image_width_entered');
	if (ACP3_Validate::isNumber($_POST['thumbheight']) === false || ACP3_Validate::isNumber($_POST['height']) === false || ACP3_Validate::isNumber($_POST['maxheight']) === false)
		$errors[] = $lang->t('gallery', 'invalid_image_height_entered');
	if (ACP3_Validate::isNumber($_POST['filesize']) === false)
		$errors['filesize'] = $lang->t('gallery', 'invalid_image_filesize_entered');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
	} else {
		$bool = ACP3_Config::module('gallery', $_POST);

		// Falls sich die anzuzeigenden Bildgrößen geändert haben, die gecacheten Bilder löschen
		if ($_POST['thumbwidth'] !== $settings['thumbwidth'] || $_POST['thumbheight'] !== $settings['thumbheight'] ||
			$_POST['width'] !== $settings['width'] || $_POST['height'] !== $settings['height']) {
			ACP3_Cache::purge('images', 'gallery');
		}

		$session->unsetFormToken();

		setRedirectMessage($bool === true ? $lang->t('common', 'settings_success') : $lang->t('common', 'settings_error'), 'acp/gallery');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	if ($comments_active === true) {
		$comments = array();
		$comments[0]['value'] = '1';
		$comments[0]['checked'] = selectEntry('comments', '1', $settings['comments'], 'checked');
		$comments[0]['lang'] = $lang->t('common', 'yes');
		$comments[1]['value'] = '0';
		$comments[1]['checked'] = selectEntry('comments', '0', $settings['comments'], 'checked');
		$comments[1]['lang'] = $lang->t('common', 'no');
		$tpl->assign('comments', $comments);
	}

	$overlay = array();
	$overlay[0]['value'] = '1';
	$overlay[0]['checked'] = selectEntry('overlay', '1', $settings['overlay'], 'checked');
	$overlay[0]['lang'] = $lang->t('common', 'yes');
	$overlay[1]['value'] = '0';
	$overlay[1]['checked'] = selectEntry('overlay', '0', $settings['overlay'], 'checked');
	$overlay[1]['lang'] = $lang->t('common', 'no');
	$tpl->assign('overlay', $overlay);

	$tpl->assign('dateformat', $date->dateformatDropdown($settings['dateformat']));

	$tpl->assign('sidebar_entries', recordsPerPage((int) $settings['sidebar'], 1, 10));

	$tpl->assign('form', isset($_POST['submit']) ? $_POST : $settings);

	$session->generateFormToken();

	ACP3_View::setContent(ACP3_View::fetchTemplate('gallery/settings.tpl'));
}
