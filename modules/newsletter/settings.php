<?php
/**
 * Newsletter
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (isset($_POST['submit'])) {
	$form = $_POST['form'];

	if (!$validate->email($form['mail']))
		$errors[] = lang('common', 'wrong_email_format');

	if (isset($errors)) {
		combo_box($errors);
	} else {
		$bool = $config->module('newsletter', $form);

		$content = combo_box($bool ? lang('newsletter', 'edit_success') : lang('newsletter', 'edit_error'), uri('acp/newsletter'));
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	$settings = $config->output('newsletter');

	$tpl->assign('form', isset($form) ? $form : $settings);

	$content = $tpl->fetch('newsletter/settings.html');
}
?>