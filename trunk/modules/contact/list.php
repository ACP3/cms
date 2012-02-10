<?php
/**
 * Contact
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

if (isset($_POST['form']) === true) {
	$form = $_POST['form'];

	if (empty($form['name']))
		$errors[] = $lang->t('common', 'name_to_short');
	if (validate::email($form['mail']) === false)
		$errors[] = $lang->t('common', 'wrong_email_format');
	if (strlen($form['message']) < 3)
		$errors[] = $lang->t('common', 'message_to_short');
	if ($auth->isUser() === false && validate::captcha($form['captcha']) === false)
		$errors[] = $lang->t('captcha', 'invalid_captcha_entered');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (validate::formToken() === false) {
		view::setContent(errorBox($lang->t('common', 'form_already_submitted')));
	} else {
		$settings = config::getModuleSettings('contact');

		$subject = sprintf($lang->t('contact', 'contact_subject'), CONFIG_SEO_TITLE);
		$body = str_replace(array('{name}', '{mail}', '{message}', '\n'), array($form['name'], $form['mail'], $form['message'], "\n"), $lang->t('contact', 'contact_body'));

		$bool = generateEmail('', $settings['mail'], $form['mail'], $subject, $body);

		$session->unsetFormToken();

		view::setContent(confirmBox($bool === true ? $lang->t('contact', 'send_mail_success') : $lang->t('contact', 'send_mail_error'), $uri->route('contact')));
	}
}
if (isset($_POST['form']) === false || isset($errors) === true && is_array($errors) === true) {
	// Falls Benutzer eingeloggt ist, Formular schon teilweise ausfüllen
	if ($auth->isUser() === true) {
		$defaults = $auth->getUserInfo();
		$disabled = ' readonly="readonly" class="readonly"';
		$defaults['name'] = !empty($defaults['realname']) ? $db->escape($defaults['realname'], 3) : $db->escape($defaults['nickname'], 3);
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
	$tpl->assign('form', isset($form) ? array_merge($defaults, $form) : $defaults);

	$tpl->assign('captcha', captcha());

	$session->generateFormToken();

	view::setContent(view::fetchTemplate('contact/list.tpl'));
}
