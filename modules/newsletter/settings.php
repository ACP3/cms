<?php
/**
 * Newsletter
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['form'])) {
	$form = $_POST['form'];

	if (!validate::email($form['mail']))
		$errors[] = $lang->t('common', 'wrong_email_format');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$bool = config::module('newsletter', $form);

		$content = comboBox($bool ? $lang->t('common', 'settings_success') : $lang->t('common', 'settings_error'), $uri->route('acp/newsletter'));
	}
}
if (!isset($_POST['form']) || isset($errors) && is_array($errors)) {
	$settings = config::getModuleSettings('newsletter');

	$tpl->assign('form', isset($form) ? $form : $settings);

	$content = modules::fetchTemplate('newsletter/settings.tpl');
}
