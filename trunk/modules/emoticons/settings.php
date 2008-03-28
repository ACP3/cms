<?php
/**
 * Emoticons
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (isset($_POST['submit'])) {
	$form = $_POST['form'];
	
	if (!$validate->isNumber($form['width']))
		$errors[] = lang('emoticons', 'invalid_image_width_entered');
	if (!$validate->isNumber($form['height']))
		$errors[] = lang('emoticons', 'invalid_image_height_entered');
	if (!$validate->isNumber($form['filesize']))
		$errors[] = lang('emoticons', 'invalid_image_filesize_entered');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$bool = $config->module('emoticons', $form);
		
		$content = comboBox($bool ? lang('emoticons', 'settings_success') : lang('emoticons', 'settings_error'), uri('acp/emoticons'));
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	$settings = $config->output('emoticons');
	
	$tpl->assign('form', isset($form) ? $form : $settings);

	$content = $tpl->fetch('emoticons/settings.html');
}
?>