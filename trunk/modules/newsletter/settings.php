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

	if (!validate::email($form['mail']))
		$errors[] = $lang->t('common', 'wrong_email_format');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$bool = config::module('newsletter', $form);

		$content = comboBox($bool ? $lang->t('newsletter', 'edit_success') : $lang->t('newsletter', 'edit_error'), uri('acp/newsletter'));
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	$settings = config::output('newsletter');

	$tpl->assign('form', isset($form) ? $form : $settings);

	$content = $tpl->fetch('newsletter/settings.html');
}
?>