<?php
/**
 * Newsletter
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit();

if (isset($_POST['form'])) {
	switch ($uri->action) {
		case 'subscribe' :
			$form = $_POST['form'];

			if (!validate::email($form['mail']))
				$errors[] = $lang->t('common', 'wrong_email_format');
			if (validate::email($form['mail']) && $db->countRows('*', 'newsletter_accounts', 'mail = \'' . $form['mail'] . '\'') == 1)
				$errors[] = $lang->t('newsletter', 'account_exists');
			if (!$auth->isUser() && !validate::captcha($form['captcha'], $form['hash']))
				$errors[] = $lang->t('captcha', 'invalid_captcha_entered');

			if (isset($errors)) {
				$tpl->assign('error_msg', comboBox($errors));
			} else {
				require ACP3_ROOT . 'modules/newsletter/functions.php';
				$bool = subscribeToNewsletter($form['mail']);

				$content = comboBox($bool ? $lang->t('newsletter', 'subscribe_success') : $lang->t('newsletter', 'subscribe_error'), ROOT_DIR);
			}
			break;
		case 'unsubscribe' :
			$form = $_POST['form'];

			if (!validate::email($form['mail']))
				$errors[] = $lang->t('common', 'wrong_email_format');
			if (validate::email($form['mail']) && $db->countRows('*', 'newsletter_accounts', 'mail = \'' . $form['mail'] . '\'') != 1)
				$errors[] = $lang->t('newsletter', 'account_not_exists');
			if (!$auth->isUser() && !validate::captcha($form['captcha'], $form['hash']))
				$errors[] = $lang->t('captcha', 'invalid_captcha_entered');

			if (isset($errors)) {
				$tpl->assign('error_msg', comboBox($errors));
			} else {
				$bool = $db->delete('newsletter_accounts', 'mail = \'' . $form['mail'] . '\'');

				$content = comboBox($bool !== null ? $lang->t('newsletter', 'unsubscribe_success') : $lang->t('newsletter', 'unsubscribe_error'), ROOT_DIR);
			}
			break;
		default:
			redirect('errors/404');
	}
}
if (!isset($_POST['form']) || isset($errors) && is_array($errors)) {
	$tpl->assign('form', isset($form) ? $form : array('mail' => ''));

	$field_value = $uri->action ? $uri->action : 'subscribe';

	$actions[0]['value'] = 'subscribe';
	$actions[0]['checked'] = selectEntry('action', 'subscribe', $field_value, 'checked');
	$actions[0]['lang'] = $lang->t('newsletter', 'subscribe');
	$actions[1]['value'] = 'unsubscribe';
	$actions[1]['checked'] = selectEntry('action', 'unsubscribe', $field_value, 'checked');
	$actions[1]['lang'] = $lang->t('newsletter', 'unsubscribe');
	$tpl->assign('actions', $actions);

	$tpl->assign('captcha', captcha());

	$content = modules::fetchTemplate('newsletter/create.html');
}