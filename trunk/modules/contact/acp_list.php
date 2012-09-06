<?php
/**
 * Contact
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['submit']) === true) {
	if (!empty($_POST['mail']) && ACP3_Validate::email($_POST['mail']) === false)
		$errors['mail'] = ACP3_CMS::$lang->t('system', 'wrong_email_format');

	if (isset($errors) === true) {
		ACP3_CMS::$view->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
	} else {
		$data = array(
			'address' => $_POST['address'],
			'mail' => $_POST['mail'],
			'telephone' => $_POST['telephone'],
			'fax' => $_POST['fax'],
			'disclaimer' => $_POST['disclaimer'],
		);

		$bool = ACP3_Config::setSettings('contact', $data);

		ACP3_CMS::$session->unsetFormToken();

		setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/contact');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	getRedirectMessage();

	$settings = ACP3_Config::getSettings('contact');

	ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : $settings);

	ACP3_CMS::$session->generateFormToken();

	ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('contact/acp_list.tpl'));
}
