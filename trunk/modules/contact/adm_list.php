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

if (isset($_POST['form'])) {
	$form = $_POST['form'];

	if (!empty($form['mail']) && !validate::email($form['mail']))
		$errors[] = $lang->t('common', 'wrong_email_format');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$form['address'] = db::escape($form['address'], 2);
		$form['telephone'] = db::escape($form['telephone']);
		$form['fax'] = db::escape($form['fax']);
		$form['disclaimer'] = db::escape($form['disclaimer'], 2);
		$form['layout'] = db::escape($form['layout'], 2);

		$bool = config::module('contact', $form);

		$content = comboBox($bool ? $lang->t('common', 'settings_success') : $lang->t('common', 'settings_error'), uri('acp/contact'));
	}
}
if (!isset($_POST['form']) || isset($errors) && is_array($errors)) {
	$contact = config::output('contact');
	$contact['address'] = $contact['address'];
	$contact['disclaimer'] = db::escape($contact['disclaimer'], 3);
	$contact['layout'] = db::escape($contact['layout'], 3);

	$tpl->assign('form', isset($form) ? $form : $contact);

	$content = modules::fetchTemplate('contact/adm_list.html');
}
