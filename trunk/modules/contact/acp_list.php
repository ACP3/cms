<?php
/**
 * Contact
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['submit']) === true) {
	if (!empty($_POST['mail']) && ACP3_Validate::email($_POST['mail']) === false)
		$errors['mail'] = $lang->t('common', 'wrong_email_format');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
	} else {
		$_POST['address'] = $db->escape($_POST['address'], 2);
		$_POST['telephone'] = $db->escape($_POST['telephone']);
		$_POST['fax'] = $db->escape($_POST['fax']);
		$_POST['disclaimer'] = $db->escape($_POST['disclaimer'], 2);
		$_POST['layout'] = $db->escape($_POST['layout'], 2);

		$bool = ACP3_Config::module('contact', $_POST);

		$session->unsetFormToken();

		ACP3_View::setContent(confirmBox($bool === true ? $lang->t('common', 'settings_success') : $lang->t('common', 'settings_error'), $uri->route('acp/contact')));
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$settings = ACP3_Config::getModuleSettings('contact');
	$settings['address'] = $db->escape($settings['address'], 3);
	$settings['telephone'] = $db->escape($settings['telephone'], 3);
	$settings['fax'] = $db->escape($settings['fax'], 3);
	$settings['disclaimer'] = $db->escape($settings['disclaimer'], 3);
	$settings['layout'] = $db->escape($settings['layout'], 3);

	$tpl->assign('form', isset($_POST['submit']) ? $_POST : $settings);

	$session->generateFormToken();

	ACP3_View::setContent(ACP3_View::fetchTemplate('contact/acp_list.tpl'));
}
