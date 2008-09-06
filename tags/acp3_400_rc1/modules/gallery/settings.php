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
	
	if (!validate::isNumber($form['width']))
		$errors[] = $lang->t('gallery', 'invalid_image_width_entered');
	if (!validate::isNumber($form['height']))
		$errors[] = $lang->t('gallery', 'invalid_image_height_entered');
	if (!validate::isNumber($form['filesize']))
		$errors[] = $lang->t('gallery', 'invalid_image_filesize_entered');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$bool = config::module('gallery', $form);
		
		$content = comboBox($bool ? $lang->t('gallery', 'settings_success') : $lang->t('gallery', 'settings_error'), uri('acp/gallery'));
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	$settings = config::output('gallery');
	
	$tpl->assign('form', isset($form) ? $form : $settings);

	$content = $tpl->fetch('gallery/settings.html');
}
?>