<?php
/**
 * Comments
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

$emoticons_active = ACP3_Modules::isActive('emoticons');

if (isset($_POST['submit']) === true) {
	if (empty($_POST['dateformat']) || ($_POST['dateformat'] !== 'long' && $_POST['dateformat'] !== 'short'))
		$errors['dateformat'] = ACP3_CMS::$lang->t('system', 'select_date_format');
	if ($emoticons_active === true && (!isset($_POST['emoticons']) || ($_POST['emoticons'] != 0 && $_POST['emoticons'] != 1)))
		$errors[] = ACP3_CMS::$lang->t('comments', 'select_emoticons');

	if (isset($errors) === true) {
		ACP3_CMS::$view->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_CMS::$view->setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
	} else {
		$data = array(
			'dateformat' => str_encode($_POST['dateformat']),
			'emoticons' => $_POST['emoticons'],
		);
		$bool = ACP3_Config::setSettings('comments', $data);

		ACP3_CMS::$session->unsetFormToken();

		setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/comments');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$settings = ACP3_Config::getSettings('comments');

	ACP3_CMS::$view->assign('dateformat', ACP3_CMS::$date->dateformatDropdown($settings['dateformat']));

	// Emoticons erlauben
	if ($emoticons_active === true) {
		$allow_emoticons = array();
		$allow_emoticons[0]['value'] = '1';
		$allow_emoticons[0]['checked'] = selectEntry('emoticons', '1', $settings['emoticons'], 'checked');
		$allow_emoticons[0]['lang'] = ACP3_CMS::$lang->t('system', 'yes');
		$allow_emoticons[1]['value'] = '0';
		$allow_emoticons[1]['checked'] = selectEntry('emoticons', '0', $settings['emoticons'], 'checked');
		$allow_emoticons[1]['lang'] = ACP3_CMS::$lang->t('system', 'no');
		ACP3_CMS::$view->assign('allow_emoticons', $allow_emoticons);
	}

	ACP3_CMS::$session->generateFormToken();
}