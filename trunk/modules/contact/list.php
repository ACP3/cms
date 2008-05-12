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
	if (!validate::email($form['mail']))
		$errors[] = lang('common', 'wrong_email_format');
	if (strlen($form['message']) < 3)
		$errors[] = lang('common', 'message_to_short');
	if (!validate::captcha($form['captcha'], $form['hash']))
		$errors[] = lang('captcha', 'invalid_captcha_entered');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$contact = config::output('contact');

		$subject = sprintf(lang('contact', 'contact_subject'), CONFIG_TITLE);
		$body = str_replace(array('{name}', '{mail}', '{message}', '\n'), array($form['name'], $form['mail'], $form['message'], "\n"), lang('contact', 'contact_body'));
		$header = "Content-type: text/plain; charset=UTF-8\r\n";
		$header.= 'FROM:' . $form['mail'];

		$bool = @mail($contact['mail'], $subject, $body, $header);

		$content = comboBox($bool ? lang('contact', 'send_mail_success') : lang('contact', 'send_mail_error'), uri('contact'));
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	// Falls Benutzer eingeloggt ist, Formular schon teilweise ausfüllen
	if ($auth->isUser()) {
		$defaults = $auth->getUserInfo();
		$disabled = ' readonly="readonly" class="readonly"';
		$defaults['name'] = !empty($defaults['fullname']) ? $db->escape($defaults['fullname'], 3) : $db->escape($defaults['nickname'], 3);
		$defaults['message'] = '';

		if (isset($form)) {
			$form['name_disabled'] = $disabled;
			$form['mail_disabled'] = $disabled;
		} else {
			$defaults['name_disabled'] = $disabled;
			$defaults['mail_disabled'] = $disabled;
		}
	} else {
		$defaults = array(
			'name' => '',
			'name_disabled' => '',
			'mail' => '',
			'mail_disabled' => '',
			'message' => '',
		);
	}
	$tpl->assign('form', isset($form) ? $form : $defaults);
	
	$tpl->assign('captcha', captcha());

	$content = $tpl->fetch('contact/list.html');
}
?>