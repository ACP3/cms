<?php
/**
 * Newsletter
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3'))
	exit;

if (isset($_POST['submit'])) {
	switch ($modules->action) {
		case 'subscribe':
			$form = $_POST['form'];

			if (!$validate->email($form['mail']))
				$errors[] = lang('common', 'wrong_email_format');
			if ($validate->email($form['mail']) && $db->select('id', 'nl_accounts', 'mail = \'' . $form['mail'] . '\'', 0, 0, 0, 1) == 1)
				$errors[] = lang('newsletter', 'nl_account_exists');

			if (isset($errors)) {
				$tpl->assign('error_msg', combo_box($errors));
			} else {
				$time = explode(' ', microtime());
				$hash = md5(mt_rand(0, $time['1']));

				$text = sprintf(lang('newsletter', 'nl_subscribe_body'), $form['mail'], $_SERVER['HTTP_HOST']);
				$text.= 'http://' . $_SERVER['HTTP_HOST'] . uri('newsletter/activate/hash_' . $hash . '/mail_' . $form['mail']);

				$insert_values = array(
					'id' => '',
					'mail' => $form['mail'],
					'hash' => $hash,
				);

				$bool = $db->insert('nl_accounts', $insert_values);

				$nl_mail = $config->output('newsletter');
				$bool2 = @mail($form['mail'], sprintf(lang('newsletter', 'nl_subscribe_subject'), $_SERVER['HTTP_HOST']), $text, 'FROM:' . $nl_mail['mail']);

				$content = combo_box($bool && $bool2 ? lang('newsletter', 'nl_subscribe_success') : lang('newsletter', 'nl_subscribe_error'), ROOT_DIR);
			}
			break;
		case 'unsubscribe':
			$form = $_POST['form'];

			if (!$validate->email($form['mail']))
				$errors[] = lang('common', 'wrong_email_format');
			if ($validate->email($form['mail']) && $db->select('id', 'nl_accounts', 'mail = \'' . $form['mail'] . '\'', 0, 0, 0, 1) != 1)
				$errors[] = lang('newsletter', 'nl_account_not_exists');

			if (isset($errors)) {
				$tpl->assign('error_msg', combo_box($errors));
			} else {
				$bool = $db->delete('nl_accounts', 'mail = \'' . $form['mail'] . '\'');

				$content = combo_box($bool ? lang('newsletter', 'nl_unsubscribe_success') : lang('newsletter', 'nl_unsubscribe_error'), ROOT_DIR);
			}
			break;
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	$tpl->assign('form', isset($form) ? $form : '');

	$field_value = isset($_POST['action']) ? $_POST['action'] : 'subscribe';

	$actions[0]['value'] = 'subscribe';
	$actions[0]['checked'] = select_entry('action', 'subscribe', $field_value, 'checked');
	$actions[0]['lang'] = lang('newsletter', 'subscribe');
	$actions[1]['value'] = 'unsubscribe';
	$actions[1]['checked'] = select_entry('action', 'unsubscribe', $field_value, 'checked');
	$actions[1]['lang'] = lang('newsletter', 'unsubscribe');
	$tpl->assign('actions', $actions);

	$content = $tpl->fetch('newsletter/create.html');
}
?>