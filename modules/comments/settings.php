<?php
/**
 * Comments
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['form']) === true) {
	$form = $_POST['form'];

	if (empty($form['dateformat']) || ($form['dateformat'] != 'long' && $form['dateformat'] != 'short'))
		$errors[] = $lang->t('common', 'select_date_format');
	if (!validate::formToken())
		$errors[] = $lang->t('common', 'form_already_submitted');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$bool = config::module('comments', $form);

		$session->unsetFormToken();

		view::setContent(comboBox($bool ? $lang->t('common', 'settings_success') : $lang->t('common', 'settings_error'), $uri->route('acp/files')));
	}
}
if (isset($_POST['form']) === false || isset($errors) === true && is_array($errors) === true) {
	$settings = config::getModuleSettings('comments');

	$dateformat[0]['value'] = 'short';
	$dateformat[0]['selected'] = selectEntry('dateformat', 'short', $settings['dateformat']);
	$dateformat[0]['lang'] = $lang->t('common', 'date_format_short');
	$dateformat[1]['value'] = 'long';
	$dateformat[1]['selected'] = selectEntry('dateformat', 'long', $settings['dateformat']);
	$dateformat[1]['lang'] = $lang->t('common', 'date_format_long');
	$tpl->assign('dateformat', $dateformat);

	$session->generateFormToken();

	view::setContent(view::fetchTemplate('comments/settings.tpl'));
}