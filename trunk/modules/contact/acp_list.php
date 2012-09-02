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
		$errors['mail'] = ACP3_CMS::$lang->t('common', 'wrong_email_format');

	if (isset($errors) === true) {
		ACP3_CMS::$view->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('common', 'form_already_submitted')));
	} else {
		$_POST['address'] = ACP3_CMS::$db->escape($_POST['address'], 2);
		$_POST['telephone'] = ACP3_CMS::$db->escape($_POST['telephone']);
		$_POST['fax'] = ACP3_CMS::$db->escape($_POST['fax']);
		$_POST['disclaimer'] = ACP3_CMS::$db->escape($_POST['disclaimer'], 2);

		$bool = ACP3_Config::setSettings('contact', $_POST);

		ACP3_CMS::$session->unsetFormToken();

		ACP3_CMS::setContent(confirmBox($bool === true ? ACP3_CMS::$lang->t('common', 'settings_success') : ACP3_CMS::$lang->t('common', 'settings_error'), ACP3_CMS::$uri->route('acp/contact')));
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$settings = ACP3_Config::getSettings('contact');
	$settings['address'] = ACP3_CMS::$db->escape($settings['address'], 3);
	$settings['telephone'] = ACP3_CMS::$db->escape($settings['telephone'], 3);
	$settings['fax'] = ACP3_CMS::$db->escape($settings['fax'], 3);
	$settings['disclaimer'] = ACP3_CMS::$db->escape($settings['disclaimer'], 3);

	ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : $settings);

	ACP3_CMS::$session->generateFormToken();

	ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('contact/acp_list.tpl'));
}
