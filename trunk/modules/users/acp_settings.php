<?php
/**
 * Users
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['submit']) === true) {
	if (!isset($_POST['language_override']) || $_POST['language_override'] != 1 && $_POST['language_override'] != 0)
		$errors[] = $lang->t('users', 'select_languages_override');
	if (!isset($_POST['entries_override']) || $_POST['entries_override'] != 1 && $_POST['entries_override'] != 0)
		$errors[] = $lang->t('users', 'select_entries_override');
	if (!isset($_POST['enable_registration']) || $_POST['enable_registration'] != 1 && $_POST['enable_registration'] != 0)
		$errors[] = $lang->t('users', 'select_enable_registration');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
	} else {
		$bool = ACP3_Config::module('users', $_POST);

		$session->unsetFormToken();

		setRedirectMessage($bool === true ? $lang->t('common', 'settings_success') : $lang->t('common', 'settings_error'), 'acp/users');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$settings = ACP3_Config::getModuleSettings('users');

	$languages = array();
	$languages[0]['value'] = '1';
	$languages[0]['checked'] = selectEntry('language_override', '1', $settings['language_override'], 'checked');
	$languages[0]['lang'] = $lang->t('common', 'yes');
	$languages[1]['value'] = '0';
	$languages[1]['checked'] = selectEntry('language_override', '0', $settings['language_override'], 'checked');
	$languages[1]['lang'] = $lang->t('common', 'no');
	$tpl->assign('languages', $languages);

	$entries = array();
	$entries[0]['value'] = '1';
	$entries[0]['checked'] = selectEntry('entries_override', '1', $settings['entries_override'], 'checked');
	$entries[0]['lang'] = $lang->t('common', 'yes');
	$entries[1]['value'] = '0';
	$entries[1]['checked'] = selectEntry('entries_override', '0', $settings['entries_override'], 'checked');
	$entries[1]['lang'] = $lang->t('common', 'no');
	$tpl->assign('entries', $entries);

	$registration = array();
	$registration[0]['value'] = '1';
	$registration[0]['checked'] = selectEntry('enable_registration', '1', $settings['enable_registration'], 'checked');
	$registration[0]['lang'] = $lang->t('common', 'yes');
	$registration[1]['value'] = '0';
	$registration[1]['checked'] = selectEntry('enable_registration', '0', $settings['enable_registration'], 'checked');
	$registration[1]['lang'] = $lang->t('common', 'no');
	$tpl->assign('registration', $registration);

	$session->generateFormToken();

	ACP3_View::setContent(ACP3_View::fetchTemplate('users/acp_settings.tpl'));
}