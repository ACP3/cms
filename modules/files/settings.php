<?php
/**
 * Files
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

$comments_active = modules::isActive('comments');

if (isset($_POST['form']) === true) {
	$form = $_POST['form'];

	if (empty($form['dateformat']) || ($form['dateformat'] !== 'long' && $form['dateformat'] !== 'short'))
		$errors['dateformat'] = $lang->t('common', 'select_date_format');
	if (validate::isNumber($form['sidebar']) === false)
		$errors['sidebar'] = $lang->t('common', 'select_sidebar_entries');
	if ($comments_active === true && (!isset($form['comments']) || $form['comments'] != 1 && $form['comments'] != 0))
		$errors[] = $lang->t('files', 'select_allow_comments');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (validate::formToken() === false) {
		view::setContent(errorBox($lang->t('common', 'form_already_submitted')));
	} else {
		$bool = config::module('files', $form);

		$session->unsetFormToken();

		setRedirectMessage($bool === true ? $lang->t('common', 'settings_success') : $lang->t('common', 'settings_error'), 'acp/files');
	}
}
if (isset($_POST['form']) === false || isset($errors) === true && is_array($errors) === true) {
	$settings = config::getModuleSettings('files');

	if ($comments_active === true) {
		$comments = array();
		$comments[0]['value'] = '1';
		$comments[0]['checked'] = selectEntry('comments', '1', $settings['comments'], 'checked');
		$comments[0]['lang'] = $lang->t('common', 'yes');
		$comments[1]['value'] = '0';
		$comments[1]['checked'] = selectEntry('comments', '0', $settings['comments'], 'checked');
		$comments[1]['lang'] = $lang->t('common', 'no');
		$tpl->assign('comments', $comments);
	}

	$tpl->assign('dateformat', $date->dateformatDropdown($settings['dateformat']));

	$tpl->assign('sidebar_entries', recordsPerPage((int) $settings['sidebar'], 1, 10));

	$session->generateFormToken();

	view::setContent(view::fetchTemplate('files/settings.tpl'));
}