<?php
/**
 * Newsletter
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit();

$captchaAccess = ACP3_Modules::check('captcha', 'functions');

if (isset($_POST['submit']) === true) {
	switch (ACP3_CMS::$uri->action) {
		case 'subscribe':
			if (ACP3_Validate::email($_POST['mail']) === false)
				$errors['mail'] = ACP3_CMS::$lang->t('system', 'wrong_email_format');
			if (ACP3_Validate::email($_POST['mail']) && ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'newsletter_accounts WHERE mail = ?', array($_POST['mail'])) == 1)
				$errors['mail'] = ACP3_CMS::$lang->t('newsletter', 'account_exists');
			if ($captchaAccess === true && ACP3_CMS::$auth->isUser() === false && ACP3_Validate::captcha($_POST['captcha']) === false)
				$errors['captcha'] = ACP3_CMS::$lang->t('captcha', 'invalid_captcha_entered');

			if (isset($errors) === true) {
				ACP3_CMS::$view->assign('error_msg', errorBox($errors));
			} elseif (ACP3_Validate::formToken() === false) {
				ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
			} else {
				require MODULES_DIR . 'newsletter/functions.php';
				$bool = subscribeToNewsletter($_POST['mail']);

				ACP3_CMS::$session->unsetFormToken();

				ACP3_CMS::setContent(confirmBox(ACP3_CMS::$lang->t('newsletter', $bool !== false ? 'subscribe_success' : 'subscribe_error'), ROOT_DIR));
			}
			break;
		case 'unsubscribe':
			if (ACP3_Validate::email($_POST['mail']) === false)
				$errors[] = ACP3_CMS::$lang->t('system', 'wrong_email_format');
			if (ACP3_Validate::email($_POST['mail']) && ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'newsletter_accounts WHERE mail = ?', array($_POST['mail'])) != 1)
				$errors[] = ACP3_CMS::$lang->t('newsletter', 'account_not_exists');
			if ($captchaAccess === true && ACP3_CMS::$auth->isUser() === false && ACP3_Validate::captcha($_POST['captcha']) === false)
				$errors[] = ACP3_CMS::$lang->t('captcha', 'invalid_captcha_entered');

			if (isset($errors) === true) {
				ACP3_CMS::$view->assign('error_msg', errorBox($errors));
			} elseif (ACP3_Validate::formToken() === false) {
				ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
			} else {
				$bool = ACP3_CMS::$db2->delete(DB_PRE . 'newsletter_accounts', array('mail' => $_POST['mail']));

				ACP3_CMS::$session->unsetFormToken();

				ACP3_CMS::setContent(confirmBox(ACP3_CMS::$lang->t('newsletter', $bool !== false ? 'unsubscribe_success' : 'unsubscribe_error'), ROOT_DIR));
			}
			break;
		default:
			ACP3_CMS::$uri->redirect('errors/404');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : array('mail' => ''));

	$field_value = ACP3_CMS::$uri->action ? ACP3_CMS::$uri->action : 'subscribe';

	$actions = array();
	$actions[0]['value'] = 'subscribe';
	$actions[0]['checked'] = selectEntry('action', 'subscribe', $field_value, 'checked');
	$actions[0]['lang'] = ACP3_CMS::$lang->t('newsletter', 'subscribe');
	$actions[1]['value'] = 'unsubscribe';
	$actions[1]['checked'] = selectEntry('action', 'unsubscribe', $field_value, 'checked');
	$actions[1]['lang'] = ACP3_CMS::$lang->t('newsletter', 'unsubscribe');
	ACP3_CMS::$view->assign('actions', $actions);

	if ($captchaAccess === true) {
		require_once MODULES_DIR . 'captcha/functions.php';
		ACP3_CMS::$view->assign('captcha', captcha());
	}

	ACP3_CMS::$session->generateFormToken();
}