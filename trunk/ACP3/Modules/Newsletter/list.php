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

$captchaAccess = ACP3\Core\Modules::check('captcha', 'functions');

if (isset($_POST['submit']) === true) {
	switch (ACP3\CMS::$injector['URI']->action) {
		case 'subscribe':
			if (ACP3\Core\Validate::email($_POST['mail']) === false)
				$errors['mail'] = ACP3\CMS::$injector['Lang']->t('system', 'wrong_email_format');
			if (ACP3\Core\Validate::email($_POST['mail']) && ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'newsletter_accounts WHERE mail = ?', array($_POST['mail'])) == 1)
				$errors['mail'] = ACP3\CMS::$injector['Lang']->t('newsletter', 'account_exists');
			if ($captchaAccess === true && ACP3\CMS::$injector['Auth']->isUser() === false && ACP3\Core\Validate::captcha($_POST['captcha']) === false)
				$errors['captcha'] = ACP3\CMS::$injector['Lang']->t('captcha', 'invalid_captcha_entered');

			if (isset($errors) === true) {
				ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (ACP3\Core\Validate::formToken() === false) {
				ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted')));
			} else {
				require MODULES_DIR . 'newsletter/functions.php';
				$bool = subscribeToNewsletter($_POST['mail']);

				ACP3\CMS::$injector['Session']->unsetFormToken();

				ACP3\CMS::$injector['View']->setContent(confirmBox(ACP3\CMS::$injector['Lang']->t('newsletter', $bool !== false ? 'subscribe_success' : 'subscribe_error'), ROOT_DIR));
			}
			break;
		case 'unsubscribe':
			if (ACP3\Core\Validate::email($_POST['mail']) === false)
				$errors[] = ACP3\CMS::$injector['Lang']->t('system', 'wrong_email_format');
			if (ACP3\Core\Validate::email($_POST['mail']) && ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'newsletter_accounts WHERE mail = ?', array($_POST['mail'])) != 1)
				$errors[] = ACP3\CMS::$injector['Lang']->t('newsletter', 'account_not_exists');
			if ($captchaAccess === true && ACP3\CMS::$injector['Auth']->isUser() === false && ACP3\Core\Validate::captcha($_POST['captcha']) === false)
				$errors[] = ACP3\CMS::$injector['Lang']->t('captcha', 'invalid_captcha_entered');

			if (isset($errors) === true) {
				ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
			} elseif (ACP3\Core\Validate::formToken() === false) {
				ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted')));
			} else {
				$bool = ACP3\CMS::$injector['Db']->delete(DB_PRE . 'newsletter_accounts', array('mail' => $_POST['mail']));

				ACP3\CMS::$injector['Session']->unsetFormToken();

				ACP3\CMS::$injector['View']->setContent(confirmBox(ACP3\CMS::$injector['Lang']->t('newsletter', $bool !== false ? 'unsubscribe_success' : 'unsubscribe_error'), ROOT_DIR));
			}
			break;
		default:
			ACP3\CMS::$injector['URI']->redirect('errors/404');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	ACP3\CMS::$injector['View']->assign('form', isset($_POST['submit']) ? $_POST : array('mail' => ''));

	$field_value = ACP3\CMS::$injector['URI']->action ? ACP3\CMS::$injector['URI']->action : 'subscribe';

	$actions = array();
	$actions[0]['value'] = 'subscribe';
	$actions[0]['checked'] = ACP3\Core\Functions::selectEntry('action', 'subscribe', $field_value, 'checked');
	$actions[0]['lang'] = ACP3\CMS::$injector['Lang']->t('newsletter', 'subscribe');
	$actions[1]['value'] = 'unsubscribe';
	$actions[1]['checked'] = ACP3\Core\Functions::selectEntry('action', 'unsubscribe', $field_value, 'checked');
	$actions[1]['lang'] = ACP3\CMS::$injector['Lang']->t('newsletter', 'unsubscribe');
	ACP3\CMS::$injector['View']->assign('actions', $actions);

	if ($captchaAccess === true) {
		require_once MODULES_DIR . 'captcha/functions.php';
		ACP3\CMS::$injector['View']->assign('captcha', captcha());
	}

	ACP3\CMS::$injector['Session']->generateFormToken();
}