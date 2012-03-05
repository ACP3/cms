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

$comments_active = ACP3_Modules::isActive('comments');

if (isset($_POST['submit']) === true) {
	if (empty($_POST['dateformat']) || ($_POST['dateformat'] !== 'long' && $_POST['dateformat'] !== 'short'))
		$errors['dateformat'] = $lang->t('common', 'select_date_format');
	if (ACP3_Validate::isNumber($_POST['sidebar']) === false)
		$errors['sideabr'] = $lang->t('common', 'select_sidebar_entries');
	if (!isset($_POST['readmore']) || $_POST['readmore'] != 1 && $_POST['readmore'] != 0)
		$errors[] = $lang->t('news', 'select_activate_readmore');
	if (ACP3_Validate::isNumber($_POST['readmore_chars']) === false || $_POST['readmore_chars'] == 0)
		$errors['readmore-chars'] = $lang->t('news', 'type_in_readmore_chars');
	if (!isset($_POST['category_in_breadcrumb']) || $_POST['category_in_breadcrumb'] != 1 && $_POST['category_in_breadcrumb'] != 0)
		$errors[] = $lang->t('news', 'select_display_category_in_breadcrumb');
	if ($comments_active === true && (!isset($_POST['comments']) || $_POST['comments'] != 1 && $_POST['comments'] != 0))
		$errors[] = $lang->t('news', 'select_allow_comments');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
	} else {
		$bool = ACP3_Config::module('news', $_POST);

		$session->unsetFormToken();

		setRedirectMessage($bool === true ? $lang->t('common', 'settings_success') : $lang->t('common', 'settings_error'), 'acp/news');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$settings = ACP3_Config::getModuleSettings('news');

	$tpl->assign('dateformat', $date->dateformatDropdown($settings['dateformat']));

	$readmore = array();
	$readmore[0]['value'] = '1';
	$readmore[0]['checked'] = selectEntry('readmore', '1', $settings['readmore'], 'checked');
	$readmore[0]['lang'] = $lang->t('common', 'yes');
	$readmore[1]['value'] = '0';
	$readmore[1]['checked'] = selectEntry('readmore', '0', $settings['readmore'], 'checked');
	$readmore[1]['lang'] = $lang->t('common', 'no');
	$tpl->assign('readmore', $readmore);

	$tpl->assign('readmore_chars', isset($_POST['submit']) ? $_POST['readmore_chars'] : $settings['readmore_chars']);

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

	$tpl->assign('sidebar_entries', recordsPerPage((int) $settings['sidebar'], 1, 10));

	$category_in_breadcrumb = array();
	$category_in_breadcrumb[0]['value'] = '1';
	$category_in_breadcrumb[0]['checked'] = selectEntry('category_in_breadcrumb', '1', $settings['category_in_breadcrumb'], 'checked');
	$category_in_breadcrumb[0]['lang'] = $lang->t('common', 'yes');
	$category_in_breadcrumb[1]['value'] = '0';
	$category_in_breadcrumb[1]['checked'] = selectEntry('category_in_breadcrumb', '0', $settings['category_in_breadcrumb'], 'checked');
	$category_in_breadcrumb[1]['lang'] = $lang->t('common', 'no');
	$tpl->assign('category_in_breadcrumb', $category_in_breadcrumb);

	$session->generateFormToken();

	ACP3_View::setContent(ACP3_View::fetchTemplate('news/settings.tpl'));
}