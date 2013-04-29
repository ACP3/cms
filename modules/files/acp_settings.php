<?php
/**
 * Files
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
	if ($comments_active === true && (!isset($_POST['comments']) || $_POST['comments'] != 1 && $_POST['comments'] != 0))
		$errors[] = ACP3_CMS::$lang->t('files', 'select_allow_comments');

	if (isset($errors) === true) {
		ACP3_CMS::$view->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_CMS::$view->setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
	} else {
		$data = array(
			'dateformat' => str_encode($_POST['dateformat']),
			'sidebar' => (int) $_POST['sidebar'],
			'comments' => $_POST['comments']
		);
		$bool = ACP3_Config::setSettings('files', $data);

		ACP3_CMS::$session->unsetFormToken();

		setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/files');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$settings = ACP3_Config::getSettings('files');

	if ($comments_active === true) {
		$lang_comments = array(ACP3_CMS::$lang->t('system', 'yes'), ACP3_CMS::$lang->t('system', 'no'));
		ACP3_CMS::$view->assign('comments', selectGenerator('comments', array(1, 0), $lang_comments, $settings['comments'], 'checked'));
	}

	ACP3_CMS::$view->assign('dateformat', ACP3_CMS::$date->dateformatDropdown($settings['dateformat']));

	ACP3_CMS::$view->assign('sidebar_entries', recordsPerPage((int) $settings['sidebar'], 1, 10));

	ACP3_CMS::$session->generateFormToken();
}