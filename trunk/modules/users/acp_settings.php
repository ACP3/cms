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
	if (!empty($_POST['mail']) && ACP3_Validate::email($_POST['mail']) === false)
		$errors['mail'] = ACP3_CMS::$lang->t('system', 'wrong_email_format');
	if (!isset($_POST['language_override']) || $_POST['language_override'] != 1 && $_POST['language_override'] != 0)
		$errors[] = ACP3_CMS::$lang->t('users', 'select_languages_override');
	if (!isset($_POST['entries_override']) || $_POST['entries_override'] != 1 && $_POST['entries_override'] != 0)
		$errors[] = ACP3_CMS::$lang->t('users', 'select_entries_override');
	if (!isset($_POST['enable_registration']) || $_POST['enable_registration'] != 1 && $_POST['enable_registration'] != 0)
		$errors[] = ACP3_CMS::$lang->t('users', 'select_enable_registration');

	if (isset($errors) === true) {
		ACP3_CMS::$view->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
	} else {
		$data = array(
			'enable_registration' => $_POST['enable_registration'],
			'entries_override' => $_POST['entries_override'],
			'language_override' => $_POST['language_override'],
			'mail' => $_POST['mail']
		);
		$bool = ACP3_Config::setSettings('users', $data);

		ACP3_CMS::$session->unsetFormToken();

		setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/users');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$settings = ACP3_Config::getSettings('users');

	$languages = array();
	$languages[0]['value'] = '1';
	$languages[0]['checked'] = selectEntry('language_override', '1', $settings['language_override'], 'checked');
	$languages[0]['lang'] = ACP3_CMS::$lang->t('system', 'yes');
	$languages[1]['value'] = '0';
	$languages[1]['checked'] = selectEntry('language_override', '0', $settings['language_override'], 'checked');
	$languages[1]['lang'] = ACP3_CMS::$lang->t('system', 'no');
	ACP3_CMS::$view->assign('languages', $languages);

	$entries = array();
	$entries[0]['value'] = '1';
	$entries[0]['checked'] = selectEntry('entries_override', '1', $settings['entries_override'], 'checked');
	$entries[0]['lang'] = ACP3_CMS::$lang->t('system', 'yes');
	$entries[1]['value'] = '0';
	$entries[1]['checked'] = selectEntry('entries_override', '0', $settings['entries_override'], 'checked');
	$entries[1]['lang'] = ACP3_CMS::$lang->t('system', 'no');
	ACP3_CMS::$view->assign('entries', $entries);

	$registration = array();
	$registration[0]['value'] = '1';
	$registration[0]['checked'] = selectEntry('enable_registration', '1', $settings['enable_registration'], 'checked');
	$registration[0]['lang'] = ACP3_CMS::$lang->t('system', 'yes');
	$registration[1]['value'] = '0';
	$registration[1]['checked'] = selectEntry('enable_registration', '0', $settings['enable_registration'], 'checked');
	$registration[1]['lang'] = ACP3_CMS::$lang->t('system', 'no');
	ACP3_CMS::$view->assign('registration', $registration);

	ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : array('mail' => $settings['mail']));

	ACP3_CMS::$session->generateFormToken();
}