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

$emoticons_active = modules::isActive('emoticons');

if (isset($_POST['form']) === true) {
	$form = $_POST['form'];

	if (empty($form['dateformat']) || ($form['dateformat'] !== 'long' && $form['dateformat'] !== 'short'))
		$errors['dateformat'] = $lang->t('common', 'select_date_format');
	if ($emoticons_active === true && (!isset($form['emoticons']) || ($form['emoticons'] != 0 && $form['emoticons'] != 1)))
		$errors[] = $lang->t('comments', 'select_emoticons');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (validate::formToken() === false) {
		view::setContent(errorBox($lang->t('common', 'form_already_submitted')));
	} else {
		$bool = config::module('comments', $form);

		$session->unsetFormToken();

		setRedirectMessage($bool === true ? $lang->t('common', 'settings_success') : $lang->t('common', 'settings_error'), 'acp/comments');
	}
}
if (isset($_POST['form']) === false || isset($errors) === true && is_array($errors) === true) {
	$settings = config::getModuleSettings('comments');

	$tpl->assign('dateformat', $date->dateformatDropdown($settings['dateformat']));

	// Emoticons erlauben
	if ($emoticons_active === true) {
		$allow_emoticons = array();
		$allow_emoticons[0]['value'] = '1';
		$allow_emoticons[0]['checked'] = selectEntry('emoticons', '1', $settings['emoticons'], 'checked');
		$allow_emoticons[0]['lang'] = $lang->t('common', 'yes');
		$allow_emoticons[1]['value'] = '0';
		$allow_emoticons[1]['checked'] = selectEntry('emoticons', '0', $settings['emoticons'], 'checked');
		$allow_emoticons[1]['lang'] = $lang->t('common', 'no');
		$tpl->assign('allow_emoticons', $allow_emoticons);
	}

	$session->generateFormToken();

	view::setContent(view::fetchTemplate('comments/settings.tpl'));
}