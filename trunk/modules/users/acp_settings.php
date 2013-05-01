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
		ACP3_CMS::$view->setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
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

	$lang_languages = array(ACP3_CMS::$lang->t('system', 'yes'), ACP3_CMS::$lang->t('system', 'no'));
	ACP3_CMS::$view->assign('languages', selectGenerator('language_override', array(1, 0), $lang_languages, $settings['language_override'], 'checked'));

	$lang_entries = array(ACP3_CMS::$lang->t('system', 'yes'), ACP3_CMS::$lang->t('system', 'no'));
	ACP3_CMS::$view->assign('entries', selectGenerator('entries_override', array(1, 0), $lang_entries, $settings['entries_override'], 'checked'));

	$lang_registration = array(ACP3_CMS::$lang->t('system', 'yes'), ACP3_CMS::$lang->t('system', 'no'));
	ACP3_CMS::$view->assign('registration', selectGenerator('enable_registration', array(1, 0), $lang_registration, $settings['enable_registration'], 'checked'));

	ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : array('mail' => $settings['mail']));

	ACP3_CMS::$session->generateFormToken();
}