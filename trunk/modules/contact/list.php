<?php
/**
 * Contact
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;

if (isset($_POST['submit'])) {
	$form = $_POST['form'];

	if (empty($form['name']))
		$errors[] = lang('common', 'name_to_short');
	if (!$validate->email($form['mail']))
		$errors[] = lang('common', 'wrong_email_format');
	if (strlen($form['message']) < 3)
		$errors[] = lang('common', 'message_to_short');

	if (isset($errors)) {
		combo_box($errors);
	} else {
		$contact = $config->output('contact');

		$subject = sprintf(lang('contact', 'contact_subject'), CONFIG_TITLE);
		$body = sprintf(lang('contact', 'contact_body'), $form['name'], $form['mail']) . "\n\n" . $form['message'];

		$bool = @mail($contact['mail'], $subject, $body, 'FROM:' . $form['mail']);

		$content = combo_box($bool ? lang('contact', 'send_mail_success') : lang('contact', 'send_mail_error'), uri('contact'));
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	// Falls Benutzer eingeloggt ist, Formular schon teilweise ausfüllen
	if ($auth->isUser() && preg_match('/\d/', USER_ID)) {
		$user = $auth->getUserInfo('mail');
		$disabled = ' readonly="readonly" class="readonly"';

		if (isset($form)) {
			$form['mail_disabled'] = $disabled;
		} else {
			$user['mail_disabled'] = $disabled;
		}
		$tpl->assign('form', isset($form) ? $form : $user);
	} else {
		$tpl->assign('form', isset($form) ? $form : array('mail_disabled' => ''));
	}

	$content = $tpl->fetch('contact/list.html');
}
?>