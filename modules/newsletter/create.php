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

if (isset($_POST['submit'])) {
	switch ($uri->action) {
		case 'subscribe' :
			$form = $_POST['form'];

			if (!validate::email($form['mail']))
				$errors[] = $lang->t('common', 'wrong_email_format');
			if (validate::email($form['mail']) && $db->select('id', 'newsletter_accounts', 'mail = \'' . $form['mail'] . '\'', 0, 0, 0, 1) == 1)
				$errors[] = $lang->t('newsletter', 'account_exists');
			if (!validate::captcha($form['captcha'], $form['hash']))
				$errors[] = $lang->t('captcha', 'invalid_captcha_entered');

			if (isset($errors)) {
				$tpl->assign('error_msg', comboBox($errors));
			} else {
				$hash = md5(mt_rand(0, microtime(true)));
				$host = htmlentities($_SERVER['HTTP_HOST']);
				$newsletter = config::output('newsletter');

				$text = str_replace(array('{mail}', '{title}', '{host}'), array($form['mail'], CONFIG_TITLE, $host), $lang->t('newsletter', 'subscribe_mail_body')) . "\n\n";
				$text .= 'http://' . $host . uri('newsletter/activate/hash_' . $hash . '/mail_' . $form['mail']);
				$header = "Content-type: text/plain; charset=UTF-8\r\n";
				$header.= 'FROM:' . $newsletter['mail'];
				$mail_sent = @mail($form['mail'], sprintf($lang->t('newsletter', 'subscribe_mail_subject'), $host), $text, $header);

				// Newsletter-Konto nur erstellen, wenn die E-Mail erfolgreich versendet werden konnte
				if ($mail_sent) {
					$insert_values = array('id' => '', 'mail' => $form['mail'], 'hash' => $hash);
					$bool = $db->insert('newsletter_accounts', $insert_values);
				}

				$content = comboBox($mail_sent && isset($bool) && $bool ? $lang->t('newsletter', 'subscribe_success') : $lang->t('newsletter', 'subscribe_error'), ROOT_DIR);
			}
			break;
		case 'unsubscribe' :
			$form = $_POST['form'];

			if (!validate::email($form['mail']))
				$errors[] = $lang->t('common', 'wrong_email_format');
			if (validate::email($form['mail']) && $db->select('id', 'newsletter_accounts', 'mail = \'' . $form['mail'] . '\'', 0, 0, 0, 1) != 1)
				$errors[] = $lang->t('newsletter', 'account_not_exists');
			if (!validate::captcha($form['captcha'], $form['hash']))
				$errors[] = $lang->t('captcha', 'invalid_captcha_entered');

			if (isset($errors)) {
				$tpl->assign('error_msg', comboBox($errors));
			} else {
				$bool = $db->delete('newsletter_accounts', 'mail = \'' . $form['mail'] . '\'');

				$content = comboBox($bool ? $lang->t('newsletter', 'unsubscribe_success') : $lang->t('newsletter', 'unsubscribe_error'), ROOT_DIR);
			}
			break;
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	$tpl->assign('form', isset($form) ? $form : array('mail' => ''));

	$field_value = isset($_POST['action']) ? $_POST['action'] : 'subscribe';

	$actions[0]['value'] = 'subscribe';
	$actions[0]['checked'] = selectEntry('action', 'subscribe', $field_value, 'checked');
	$actions[0]['lang'] = $lang->t('newsletter', 'subscribe');
	$actions[1]['value'] = 'unsubscribe';
	$actions[1]['checked'] = selectEntry('action', 'unsubscribe', $field_value, 'checked');
	$actions[1]['lang'] = $lang->t('newsletter', 'unsubscribe');
	$tpl->assign('actions', $actions);

	$tpl->assign('captcha', captcha());

	$content = $tpl->fetch('newsletter/create.html');
}
?>