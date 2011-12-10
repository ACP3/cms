<?php
/**
 * Categories
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (isset($_POST['form'])) {
	$form = $_POST['form'];
	
	if (!validate::isNumber($form['width']))
		$errors[] = $lang->t('categories', 'invalid_image_width_entered');
	if (!validate::isNumber($form['height']))
		$errors[] = $lang->t('categories', 'invalid_image_height_entered');
	if (!validate::isNumber($form['filesize']))
		$errors[] = $lang->t('categories', 'invalid_image_filesize_entered');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$bool = config::module('categories', $form);
		
		$content = comboBox($bool ? $lang->t('common', 'settings_success') : $lang->t('common', 'settings_error'), uri('acp/categories'));
	}
}
if (!isset($_POST['form']) || isset($errors) && is_array($errors)) {
	$settings = config::getModuleSettings('categories');
	
	$tpl->assign('form', isset($form) ? $form : $settings);

	$content = modules::fetchTemplate('categories/settings.html');
}
