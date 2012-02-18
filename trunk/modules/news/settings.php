<?php
/**
 * News
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
		$errors['sideabr'] = $lang->t('common', 'select_sidebar_entries');
	if (!isset($form['readmore']) || $form['readmore'] != 1 && $form['readmore'] != 0)
		$errors[] = $lang->t('news', 'select_activate_readmore');
	if (validate::isNumber($form['readmore_chars']) === false || $form['readmore_chars'] == 0)
		$errors['readmore-chars'] = $lang->t('news', 'type_in_readmore_chars');
	if (!isset($form['category_in_breadcrumb']) || $form['category_in_breadcrumb'] != 1 && $form['category_in_breadcrumb'] != 0)
		$errors[] = $lang->t('news', 'select_display_category_in_breadcrumb');
	if ($comments_active === true && (!isset($form['comments']) || $form['comments'] != 1 && $form['comments'] != 0))
		$errors[] = $lang->t('news', 'select_allow_comments');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (validate::formToken() === false) {
		view::setContent(errorBox($lang->t('common', 'form_already_submitted')));
	} else {
		$bool = config::module('news', $form);

		$session->unsetFormToken();

		setRedirectMessage($bool === true ? $lang->t('common', 'settings_success') : $lang->t('common', 'settings_error'), 'acp/news');
	}
}
if (isset($_POST['form']) === false || isset($errors) === true && is_array($errors) === true) {
	$settings = config::getModuleSettings('news');

	$tpl->assign('dateformat', $date->dateformatDropdown($settings['dateformat']));

	$readmore = array();
	$readmore[0]['value'] = '1';
	$readmore[0]['checked'] = selectEntry('readmore', '1', $settings['readmore'], 'checked');
	$readmore[0]['lang'] = $lang->t('common', 'yes');
	$readmore[1]['value'] = '0';
	$readmore[1]['checked'] = selectEntry('readmore', '0', $settings['readmore'], 'checked');
	$readmore[1]['lang'] = $lang->t('common', 'no');
	$tpl->assign('readmore', $readmore);

	$tpl->assign('readmore_chars', isset($form) ? $form['readmore_chars'] : $settings['readmore_chars']);

	if ($comments_active === true) {
		$allow_comments = array();
		$allow_comments[0]['value'] = '1';
		$allow_comments[0]['checked'] = selectEntry('comments', '1', $settings['comments'], 'checked');
		$allow_comments[0]['lang'] = $lang->t('common', 'yes');
		$allow_comments[1]['value'] = '0';
		$allow_comments[1]['checked'] = selectEntry('comments', '0', $settings['comments'], 'checked');
		$allow_comments[1]['lang'] = $lang->t('common', 'no');
		$tpl->assign('allow_comments', $allow_comments);
	}

	$sidebar_entries = array();
	for ($i = 0, $j = 1; $i < 10; ++$i, ++$j) {
		$sidebar_entries[$i]['value'] = $j;
		$sidebar_entries[$i]['selected'] = selectEntry('sidebar', $j, $settings['sidebar']);
	}
	$tpl->assign('sidebar_entries', $sidebar_entries);

	$category_in_breadcrumb = array();
	$category_in_breadcrumb[0]['value'] = '1';
	$category_in_breadcrumb[0]['checked'] = selectEntry('category_in_breadcrumb', '1', $settings['category_in_breadcrumb'], 'checked');
	$category_in_breadcrumb[0]['lang'] = $lang->t('common', 'yes');
	$category_in_breadcrumb[1]['value'] = '0';
	$category_in_breadcrumb[1]['checked'] = selectEntry('category_in_breadcrumb', '0', $settings['category_in_breadcrumb'], 'checked');
	$category_in_breadcrumb[1]['lang'] = $lang->t('common', 'no');
	$tpl->assign('category_in_breadcrumb', $category_in_breadcrumb);

	$session->generateFormToken();

	view::setContent(view::fetchTemplate('news/settings.tpl'));
}