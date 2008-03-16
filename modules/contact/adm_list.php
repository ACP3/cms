<?php
/**
 * Contact
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (isset($_POST['submit'])) {
	$form = $_POST['form'];

	if (!empty($form['mail']) && !$validate->email($form['mail']))
		$errors[] = lang('common', 'wrong_email_format');

	if (isset($errors)) {
		$tpl->assign('error_msg', combo_box($errors));
	} else {
		$form['address'] = $db->escape($form['address'], 2);
		$form['telephone'] = $db->escape($form['telephone']);
		$form['fax'] = $db->escape($form['fax']);
		$form['disclaimer'] = $db->escape($form['disclaimer'], 2);
		$form['miscellaneous'] = $db->escape($form['miscellaneous'], 2);

		$bool = $config->module('contact', $form);

		$content = combo_box($bool ? lang('contact', 'edit_success') : lang('contact', 'edit_error'), uri('acp/contact'));
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	$contact = $config->output('contact');
	$contact['address'] = $contact['address'];
	$contact['disclaimer'] = $db->escape($contact['disclaimer'], 3);
	$contact['miscellaneous'] = $db->escape($contact['miscellaneous'], 3);

	$tpl->assign('form', isset($form) ? $form : $contact);

	$content = $tpl->fetch('contact/adm_list.html');
}
?>