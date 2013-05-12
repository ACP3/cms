<?php
/**
 * Newsletter
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['submit']) === true) {
	if (ACP3\Core\Validate::email($_POST['mail']) === false)
		$errors['mail'] = ACP3\CMS::$injector['Lang']->t('system', 'wrong_email_format');

	if (isset($errors) === true) {
		ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
	} elseif (ACP3\Core\Validate::formToken() === false) {
		ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted')));
	} else {
		$data = array(
			'mail' => $_POST['mail'],
			'mailsig' => ACP3\Core\Functions::str_encode($_POST['mailsig'])
		);

		$bool = ACP3\Core\Config::setSettings('newsletter', $data);

		ACP3\CMS::$injector['Session']->unsetFormToken();

		ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/newsletter');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$settings = ACP3\Core\Config::getSettings('newsletter');

	ACP3\CMS::$injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $settings);

	ACP3\CMS::$injector['Session']->generateFormToken();
}
