<?php
/**
 * Contact
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['form']) === true) {
	$form = $_POST['form'];

	if (!empty($form['mail']) && !validate::email($form['mail']))
		$errors[] = $lang->t('common', 'wrong_email_format');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (validate::formToken() === false) {
		view::setContent(errorBox($lang->t('common', 'form_already_submitted')));
	} else {
		$form['address'] = $db->escape($form['address'], 2);
		$form['telephone'] = $db->escape($form['telephone']);
		$form['fax'] = $db->escape($form['fax']);
		$form['disclaimer'] = $db->escape($form['disclaimer'], 2);
		$form['layout'] = $db->escape($form['layout'], 2);

		$bool = config::module('contact', $form);

		$session->unsetFormToken();

		view::setContent(confirmBox($bool === true ? $lang->t('common', 'settings_success') : $lang->t('common', 'settings_error'), $uri->route('acp/contact')));
	}
}
if (isset($_POST['form']) === false || isset($errors) === true && is_array($errors) === true) {
	$settings = config::getModuleSettings('contact');
	$settings['address'] = $db->escape($settings['address'], 3);
	$settings['telephone'] = $db->escape($settings['telephone'], 3);
	$settings['fax'] = $db->escape($settings['fax'], 3);
	$settings['disclaimer'] = $db->escape($settings['disclaimer'], 3);
	$settings['layout'] = $db->escape($settings['layout'], 3);

	$tpl->assign('form', isset($form) ? $form : $settings);

	$session->generateFormToken();

	view::setContent(view::fetchTemplate('contact/adm_list.tpl'));
}
