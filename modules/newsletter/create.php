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

if (isset($_POST['submit']) === true) {
	switch ($uri->action) {
		case 'subscribe' :
			

			if (ACP3_Validate::email($_POST['mail']) === false)
				$errors['mail'] = $lang->t('common', 'wrong_email_format');
			if (ACP3_Validate::email($_POST['mail']) && $db->countRows('*', 'newsletter_accounts', 'mail = \'' . $_POST['mail'] . '\'') == 1)
				$errors['mail'] = $lang->t('newsletter', 'account_exists');
			if ($auth->isUser() === false && ACP3_Validate::captcha($_POST['captcha']) === false)
				$errors['captcha'] = $lang->t('captcha', 'invalid_captcha_entered');

			if (isset($errors) === true) {
				$tpl->assign('error_msg', errorBox($errors));
			} elseif (ACP3_Validate::formToken() === false) {
				ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
			} else {
				require MODULES_DIR . 'newsletter/functions.php';
				$bool = subscribeToNewsletter($_POST['mail']);

				$session->unsetFormToken();

				ACP3_View::setContent(confirmBox($bool !== false ? $lang->t('newsletter', 'subscribe_success') : $lang->t('newsletter', 'subscribe_error'), ROOT_DIR));
			}
			break;
		case 'unsubscribe' :
			

			if (ACP3_Validate::email($_POST['mail']) === false)
				$errors[] = $lang->t('common', 'wrong_email_format');
			if (ACP3_Validate::email($_POST['mail']) && $db->countRows('*', 'newsletter_accounts', 'mail = \'' . $_POST['mail'] . '\'') != 1)
				$errors[] = $lang->t('newsletter', 'account_not_exists');
			if ($auth->isUser() === false && ACP3_Validate::captcha($_POST['captcha']) === false)
				$errors[] = $lang->t('captcha', 'invalid_captcha_entered');

			if (isset($errors) === true) {
				$tpl->assign('error_msg', errorBox($errors));
			} elseif (ACP3_Validate::formToken() === false) {
				ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
			} else {
				$bool = $db->delete('newsletter_accounts', 'mail = \'' . $_POST['mail'] . '\'');

				$session->unsetFormToken();

				ACP3_View::setContent(confirmBox($bool !== false ? $lang->t('newsletter', 'unsubscribe_success') : $lang->t('newsletter', 'unsubscribe_error'), ROOT_DIR));
			}
			break;
		default:
			$uri->redirect('errors/404');
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$tpl->assign('form', isset($_POST['submit']) ? $_POST : array('mail' => ''));

	$field_value = $uri->action ? $uri->action : 'subscribe';

	$actions = array();
	$actions[0]['value'] = 'subscribe';
	$actions[0]['checked'] = selectEntry('action', 'subscribe', $field_value, 'checked');
	$actions[0]['lang'] = $lang->t('newsletter', 'subscribe');
	$actions[1]['value'] = 'unsubscribe';
	$actions[1]['checked'] = selectEntry('action', 'unsubscribe', $field_value, 'checked');
	$actions[1]['lang'] = $lang->t('newsletter', 'unsubscribe');
	$tpl->assign('actions', $actions);

	require_once MODULES_DIR . 'captcha/functions.php';
	$tpl->assign('captcha', captcha());

	$session->generateFormToken();

	ACP3_View::setContent(ACP3_View::fetchTemplate('newsletter/create.tpl'));
}