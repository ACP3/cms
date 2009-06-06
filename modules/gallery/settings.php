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
		$errors[] = $lang->t('news', 'select_allow_comments');

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

	$tpl->assign('form', isset($form) ? $form : $settings);

	$content = $tpl->fetch('gallery/settings.html');
}
?>