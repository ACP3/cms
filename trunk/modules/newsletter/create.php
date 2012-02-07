<?php
/**
 * Newsletter
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit();

if (isset($_POST['form']) === true) {
	switch ($uri->action) {
		case 'subscribe' :
			$form = $_POST['form'];

			if (validate::email($form['mail']) === false)
				$errors[] = $lang->t('common', 'wrong_email_format');
			if (validate::email($form['mail']) && $db->countRows('*', 'newsletter_accounts', 'mail = \'' . $form['mail'] . '\'') == 1)
				$errors[] = $lang->t('newsletter', 'account_exists');
			if ($auth->isUser() === false && validate::captcha($form['captcha']) === false)
				$errors[] = $lang->t('captcha', 'invalid_captcha_entered');

			if (isset($errors) === true) {
				$tpl->assign('error_msg', errorBox($errors));
			} elseif (validate::formToken() === false) {
				view::setContent(errorBox($lang->t('common', 'form_already_submitted')));
			} else {
				require MODULES_DIR . 'newsletter/functions.php';
				$bool = subscribeToNewsletter($form['mail']);

				$session->unsetFormToken();

				view::setContent(confirmBox($bool !== false ? $lang->t('newsletter', 'subscribe_success') : $lang->t('newsletter', 'subscribe_error'), ROOT_DIR));
			}
			break;
		case 'unsubscribe' :
			$form = $_POST['form'];

			if (validate::email($form['mail']) === false)
				$errors[] = $lang->t('common', 'wrong_email_format');
			if (validate::email($form['mail']) && $db->countRows('*', 'newsletter_accounts', 'mail = \'' . $form['mail'] . '\'') != 1)
				$errors[] = $lang->t('newsletter', 'account_not_exists');
			if ($auth->isUser() === false && validate::captcha($form['captcha']) === false)
				$errors[] = $lang->t('captcha', 'invalid_captcha_entered');

			if (isset($errors) === true) {
				$tpl->assign('error_msg', errorBox($errors));
			} elseif (validate::formToken() === false) {
				view::setContent(errorBox($lang->t('common', 'form_already_submitted')));
			} else {
				$bool = $db->delete('newsletter_accounts', 'mail = \'' . $form['mail'] . '\'');

				$session->unsetFormToken();

				view::setContent(confirmBox($bool !== false ? $lang->t('newsletter', 'unsubscribe_success') : $lang->t('newsletter', 'unsubscribe_error'), ROOT_DIR));
			}
			break;
		default:
			$uri->redirect('errors/404');
	}
}
if (isset($_POST['form']) === false || isset($errors) === true && is_array($errors) === true) {
	$tpl->assign('form', isset($form) ? $form : array('mail' => ''));

	$field_value = $uri->action ? $uri->action : 'subscribe';

	$actions = array();
	$actions[0]['value'] = 'subscribe';
	$actions[0]['checked'] = selectEntry('action', 'subscribe', $field_value, 'checked');
	$actions[0]['lang'] = $lang->t('newsletter', 'subscribe');
	$actions[1]['value'] = 'unsubscribe';
	$actions[1]['checked'] = selectEntry('action', 'unsubscribe', $field_value, 'checked');
	$actions[1]['lang'] = $lang->t('newsletter', 'unsubscribe');
	$tpl->assign('actions', $actions);

	$tpl->assign('captcha', captcha());

	$session->generateFormToken();

	view::setContent(view::fetchTemplate('newsletter/create.tpl'));
}