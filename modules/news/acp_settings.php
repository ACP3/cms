<?php
/**
 * News
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

$comments_active = ACP3_Modules::isActive('comments');

if (isset($_POST['submit']) === true) {
	if (empty($_POST['dateformat']) || ($_POST['dateformat'] !== 'long' && $_POST['dateformat'] !== 'short'))
		$errors['dateformat'] = ACP3_CMS::$lang->t('system', 'select_date_format');
	if (ACP3_Validate::isNumber($_POST['sidebar']) === false)
		$errors['sidebar'] = ACP3_CMS::$lang->t('system', 'select_sidebar_entries');
	if (!isset($_POST['readmore']) || $_POST['readmore'] != 1 && $_POST['readmore'] != 0)
		$errors[] = ACP3_CMS::$lang->t('news', 'select_activate_readmore');
	if (ACP3_Validate::isNumber($_POST['readmore_chars']) === false || $_POST['readmore_chars'] == 0)
		$errors['readmore-chars'] = ACP3_CMS::$lang->t('news', 'type_in_readmore_chars');
	if (!isset($_POST['category_in_breadcrumb']) || $_POST['category_in_breadcrumb'] != 1 && $_POST['category_in_breadcrumb'] != 0)
		$errors[] = ACP3_CMS::$lang->t('news', 'select_display_category_in_breadcrumb');
	if ($comments_active === true && (!isset($_POST['comments']) || $_POST['comments'] != 1 && $_POST['comments'] != 0))
		$errors[] = ACP3_CMS::$lang->t('news', 'select_allow_comments');

	if (isset($errors) === true) {
		ACP3_CMS::$view->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_CMS::$view->setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
	} else {
		$data = array(
			'dateformat' => str_encode($_POST['dateformat']),
			'sidebar' => (int) $_POST['sidebar'],
			'readmore' => $_POST['readmore'],
			'readmore_chars' => (int) $_POST['readmore_chars'],
			'category_in_breadcrumb' => $_POST['category_in_breadcrumb'],
			'comments' => $_POST['comments'],
		);
		$bool = ACP3_Config::setSettings('news', $data);

		ACP3_CMS::$session->unsetFormToken();

		setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/news');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$settings = ACP3_Config::getSettings('news');

	ACP3_CMS::$view->assign('dateformat', ACP3_CMS::$date->dateformatDropdown($settings['dateformat']));

	$lang_readmore = array(ACP3_CMS::$lang->t('system', 'yes'), ACP3_CMS::$lang->t('system', 'no'));
	ACP3_CMS::$view->assign('readmore', selectGenerator('readmore', array(1, 0), $lang_readmore, $settings['readmore'], 'checked'));

	ACP3_CMS::$view->assign('readmore_chars', isset($_POST['submit']) ? $_POST['readmore_chars'] : $settings['readmore_chars']);

	if ($comments_active === true) {
		$lang_allow_comments = array(ACP3_CMS::$lang->t('system', 'yes'), ACP3_CMS::$lang->t('system', 'no'));
		ACP3_CMS::$view->assign('allow_comments', selectGenerator('comments', array(1, 0), $lang_allow_comments, $settings['comments'], 'checked'));
	}

	ACP3_CMS::$view->assign('sidebar_entries', recordsPerPage((int) $settings['sidebar'], 1, 10));

	$lang_category_in_breadcrumb = array(ACP3_CMS::$lang->t('system', 'yes'), ACP3_CMS::$lang->t('system', 'no'));
	ACP3_CMS::$view->assign('category_in_breadcrumb', selectGenerator('category_in_breadcrumb', array(1, 0), $lang_category_in_breadcrumb, $settings['category_in_breadcrumb'], 'checked'));

	ACP3_CMS::$session->generateFormToken();
}