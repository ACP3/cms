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

$settings = config::getModuleSettings('gallery');
$comments_active = modules::isActive('comments');

if (isset($_POST['form']) === true) {
	$form = $_POST['form'];

	if (empty($form['dateformat']) || ($form['dateformat'] !== 'long' && $form['dateformat'] !== 'short'))
		$errors['dateformat'] = $lang->t('common', 'select_date_format');
	if (validate::isNumber($form['sidebar']) === false)
		$errors['sidebar'] = $lang->t('common', 'select_sidebar_entries');
	if (!isset($form['overlay']) || $form['overlay'] != 1 && $form['overlay'] != 0)
		$errors[] = $lang->t('gallery', 'select_use_overlay');
	if ($comments_active === true && (!isset($form['comments']) || $form['comments'] != 1 && $form['comments'] != 0))
		$errors[] = $lang->t('gallery', 'select_allow_comments');
	if (validate::isNumber($form['thumbwidth']) === false || validate::isNumber($form['width']) === false || validate::isNumber($form['maxwidth']) === false)
		$errors[] = $lang->t('gallery', 'invalid_image_width_entered');
	if (validate::isNumber($form['thumbheight']) === false || validate::isNumber($form['height']) === false || validate::isNumber($form['maxheight']) === false)
		$errors[] = $lang->t('gallery', 'invalid_image_height_entered');
	if (validate::isNumber($form['filesize']) === false)
		$errors['filesize'] = $lang->t('gallery', 'invalid_image_filesize_entered');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (validate::formToken() === false) {
		view::setContent(errorBox($lang->t('common', 'form_already_submitted')));
	} else {
		$bool = config::module('gallery', $form);

		// Falls sich die anzuzeigenden Bildgrößen geändert haben, die gecacheten Bilder löschen
		if ($form['thumbwidth'] !== $settings['thumbwidth'] || $form['thumbheight'] !== $settings['thumbheight'] ||
			$form['width'] !== $settings['width'] || $form['height'] !== $settings['height']) {
			cache::purge('images', 'gallery');
		}

		$session->unsetFormToken();

		setRedirectMessage($bool === true ? $lang->t('common', 'settings_success') : $lang->t('common', 'settings_error'), 'acp/gallery');
	}
}
if (isset($_POST['form']) === false || isset($errors) === true && is_array($errors) === true) {
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

	$tpl->assign('form', isset($form) ? $form : $settings);

	$session->generateFormToken();

	view::setContent(view::fetchTemplate('gallery/settings.tpl'));
}
