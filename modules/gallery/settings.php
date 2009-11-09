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

if (isset($_POST['submit'])) {
	$form = $_POST['form'];
	
	if (!validate::isNumber($form['thumbwidth']) || !validate::isNumber($form['width']) || !validate::isNumber($form['maxwidth']))
		$errors[] = $lang->t('gallery', 'invalid_image_width_entered');
	if (!validate::isNumber($form['thumbheight']) || !validate::isNumber($form['height']) || !validate::isNumber($form['maxheight']))
		$errors[] = $lang->t('gallery', 'invalid_image_height_entered');
	if (!validate::isNumber($form['filesize']))
		$errors[] = $lang->t('gallery', 'invalid_image_filesize_entered');
	if (!isset($form['comments']) || $form['comments'] != 1 && $form['comments'] != 0)
		$errors[] = $lang->t('gallery', 'select_allow_comments');
	if (!isset($form['colorbox']) || $form['colorbox'] != 1 && $form['colorbox'] != 0)
		$errors[] = $lang->t('gallery', 'select_use_colorbox');
	if (empty($form['dateformat']) || ($form['dateformat'] != 'long' && $form['dateformat'] != 'short'))
		$errors[] = $lang->t('common', 'select_date_format');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$bool = config::module('gallery', $form);
		
		$content = comboBox($bool ? $lang->t('common', 'settings_success') : $lang->t('common', 'settings_error'), uri('acp/gallery'));
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	$settings = config::output('gallery');
	
	$comments[0]['value'] = '1';
	$comments[0]['checked'] = selectEntry('comments', '1', $settings['comments'], 'checked');
	$comments[0]['lang'] = $lang->t('common', 'yes');
	$comments[1]['value'] = '0';
	$comments[1]['checked'] = selectEntry('comments', '0', $settings['comments'], 'checked');
	$comments[1]['lang'] = $lang->t('common', 'no');
	$tpl->assign('comments', $comments);

	$colorbox[0]['value'] = '1';
	$colorbox[0]['checked'] = selectEntry('colorbox', '1', $settings['colorbox'], 'checked');
	$colorbox[0]['lang'] = $lang->t('common', 'yes');
	$colorbox[1]['value'] = '0';
	$colorbox[1]['checked'] = selectEntry('colorbox', '0', $settings['colorbox'], 'checked');
	$colorbox[1]['lang'] = $lang->t('common', 'no');
	$tpl->assign('colorbox', $colorbox);

	$dateformat[0]['value'] = 'short';
	$dateformat[0]['selected'] = selectEntry('dateformat', 'short', $settings['dateformat']);
	$dateformat[0]['lang'] = $lang->t('common', 'date_format_short');
	$dateformat[1]['value'] = 'long';
	$dateformat[1]['selected'] = selectEntry('dateformat', 'long', $settings['dateformat']);
	$dateformat[1]['lang'] = $lang->t('common', 'date_format_long');
	$tpl->assign('dateformat', $dateformat);

	$tpl->assign('form', isset($form) ? $form : $settings);

	$content = $tpl->fetch('gallery/settings.html');
}
