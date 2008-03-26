<?php
/**
 * Newsletter
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (isset($_POST['submit'])) {
	$form = $_POST['form'];

	if (strlen($form['subject']) < 3)
		$errors[] = lang('newsletter', 'subject_to_short');
	if (strlen($form['text']) < 3)
		$errors[] = lang('newsletter', 'text_to_short');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$settings = $config->output('newsletter');

		//Testnewsletter
		if ($form['test'] == '1') {
			$bool = @mail($settings['mail'], $db->escape($form['subject']), $db->escape($form['text']) . $settings['mailsig'], 'FROM:' . $settings['mail'] . "\r\n" . 'Content-Type: text/plain; charset: UTF-8');
			//An alle versenden
		} else {
			$accounts = $db->select('mail', 'nl_accounts');
			$c_accounts = count($accounts);

			for ($i = 0; $i < $c_accounts; $i++) {
				$bool = @mail($accounts[$i]['mail'], $db->escape($form['subject']), $db->escape($form['text']) . $settings['mailsig'], 'FROM:' . $settings['mail'] . "\r\n" . 'Content-Type: text/plain; charset: UTF-8');
				if (!$bool)
				break;
			}
		}
		$content = comboBox($bool ? lang('newsletter', 'compose_success') : lang('newsletter', 'compose_error'), uri('acp/newsletter'));
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	$tpl->assign('form', isset($form) ? $form : '');

	$content = $tpl->fetch('newsletter/compose.html');
}
?>