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
		$errors['dateformat'] = $lang->t('common', 'select_date_format');
	if (ACP3_Validate::isNumber($_POST['sidebar']) === false)
		$errors['sidebar'] = $lang->t('common', 'select_sidebar_entries');
	if ($comments_active === true && (!isset($_POST['comments']) || $_POST['comments'] != 1 && $_POST['comments'] != 0))
		$errors[] = $lang->t('files', 'select_allow_comments');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
	} else {
		$bool = ACP3_Config::setSettings('files', $_POST);

		$session->unsetFormToken();

		setRedirectMessage($bool, $lang->t('common', $bool === true ? 'settings_success' : 'settings_error'), 'acp/files');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$settings = ACP3_Config::getSettings('files');

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

	ACP3_View::setContent(ACP3_View::fetchTemplate('files/acp_settings.tpl'));
}