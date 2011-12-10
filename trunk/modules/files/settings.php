<?php
/**
 * Files
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (isset($_POST['form'])) {
	$form = $_POST['form'];

	if (!isset($form['comments']) || $form['comments'] != 1 && $form['comments'] != 0)
		$errors[] = $lang->t('files', 'select_allow_comments');
	if (empty($form['dateformat']) || ($form['dateformat'] != 'long' && $form['dateformat'] != 'short'))
		$errors[] = $lang->t('common', 'select_date_format');
	if (!validate::isNumber($form['sidebar']))
		$errors[] = $lang->t('common', 'select_sidebar_entries');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$bool = config::module('files', $form);

		$content = comboBox($bool ? $lang->t('common', 'settings_success') : $lang->t('common', 'settings_error'), uri('acp/files'));
	}
}
if (!isset($_POST['form']) || isset($errors) && is_array($errors)) {
	$settings = config::getModuleSettings('files');

	$comments[0]['value'] = '1';
	$comments[0]['checked'] = selectEntry('comments', '1', $settings['comments'], 'checked');
	$comments[0]['lang'] = $lang->t('common', 'yes');
	$comments[1]['value'] = '0';
	$comments[1]['checked'] = selectEntry('comments', '0', $settings['comments'], 'checked');
	$comments[1]['lang'] = $lang->t('common', 'no');
	$tpl->assign('comments', $comments);

	$dateformat[0]['value'] = 'short';
	$dateformat[0]['selected'] = selectEntry('dateformat', 'short', $settings['dateformat']);
	$dateformat[0]['lang'] = $lang->t('common', 'date_format_short');
	$dateformat[1]['value'] = 'long';
	$dateformat[1]['selected'] = selectEntry('dateformat', 'long', $settings['dateformat']);
	$dateformat[1]['lang'] = $lang->t('common', 'date_format_long');
	$tpl->assign('dateformat', $dateformat);

	$sidebar_entries = array();
	for ($i = 0, $j = 1; $i < 10; ++$i, ++$j) {
		$sidebar_entries[$i]['value'] = $j;
		$sidebar_entries[$i]['selected'] = selectEntry('sidebar', $j, $settings['sidebar']);
	}
	$tpl->assign('sidebar_entries', $sidebar_entries);

	$content = modules::fetchTemplate('files/settings.html');
}