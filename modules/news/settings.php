<?php
/**
 * News
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (isset($_POST['submit'])) {
	$form = $_POST['form'];
	
	if (!isset($form['comments']) || $form['comments'] != 1 && $form['comments'] != 0)
		$errors[] = $lang->t('news', 'select_allow_comments');
	if (!isset($form['readmore']) || $form['readmore'] != 1 && $form['readmore'] != 0)
		$errors[] = $lang->t('news', 'select_activate_readmore');
	if (!validate::isNumber($form['readmore_chars']) || $form['readmore_chars'] == 0)
		$errors[] = $lang->t('news', 'type_in_readmore_chars');
	if (empty($form['dateformat']) || ($form['dateformat'] != 'long' && $form['dateformat'] != 'short'))
		$errors[] = $lang->t('common', 'select_date_format');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$bool = config::module('news', $form);
		
		$content = comboBox($bool ? $lang->t('common', 'settings_success') : $lang->t('common', 'settings_error'), uri('acp/news'));
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	$settings = config::output('news');

	$readmore[0]['value'] = '1';
	$readmore[0]['checked'] = selectEntry('readmore', '1', $settings['readmore'], 'checked');
	$readmore[0]['lang'] = $lang->t('common', 'yes');
	$readmore[1]['value'] = '0';
	$readmore[1]['checked'] = selectEntry('readmore', '0', $settings['readmore'], 'checked');
	$readmore[1]['lang'] = $lang->t('common', 'no');
	$tpl->assign('readmore', $readmore);
	
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

	$tpl->assign('readmore_chars', isset($form) ? $form['readmore_chars'] : $settings['readmore_chars']);

	$content = $tpl->fetch('news/settings.html');
}
