<?php
/**
 * Users
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['form']) === true) {
	$form = $_POST['form'];

	if (!isset($form['language_override']) || $form['language_override'] != 1 && $form['language_override'] != 0)
		$errors[] = $lang->t('users', 'select_languages_override');
	if (!isset($form['entries_override']) || $form['entries_override'] != 1 && $form['entries_override'] != 0)
		$errors[] = $lang->t('users', 'select_entries_override');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', comboBox($errors));
	} elseif (!validate::formToken()) {
		view::setContent(comboBox($lang->t('common', 'form_already_submitted')));
	} else {
		$bool = config::module('users', $form);

		$session->unsetFormToken();

		view::setContent(comboBox($bool ? $lang->t('common', 'settings_success') : $lang->t('common', 'settings_error'), $uri->route('acp/users')));
	}
}
if (isset($_POST['form']) === false || isset($errors) === true && is_array($errors) === true) {
	$settings = config::getModuleSettings('users');

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

	$session->generateFormToken();

	view::setContent(view::fetchTemplate('users/settings.tpl'));
}