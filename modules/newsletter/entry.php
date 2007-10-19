<?php
/**
 * Newsletter
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_FRONTEND') && !defined('IN_ACP'))
	exit;
if (!$modules->check('newsletter', 'entry'))
	redirect('errors/403');

switch ($modules->action) {
	case 'subscribe':
		$form = $_POST['form'];

		if (!$validate->email($form['mail']))
			$errors[] = lang('common', 'wrong_email_format');
		if ($validate->email($form['mail']) && $db->select('id', 'nl_accounts', 'mail = \'' . $form['mail'] . '\'', 0, 0, 0, 1) == 1)
			$errors[] = lang('newsletter', 'nl_account_exists');

		if (isset($errors)) {
			combo_box($errors);
		} else {
			$time = explode(' ', microtime());
			$hash = md5(mt_rand(0, $time['1']));

			$text = sprintf(lang('newsletter', 'nl_subscribe_body'), $form['mail'], $_SERVER['HTTP_HOST']);
			$text.= 'http://' . $_SERVER['HTTP_HOST'] . uri('newsletter/entry/action_activate/hash_' . $hash . '/mail_' . $form['mail']);

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
			combo_box($errors);
		} else {
			$bool = $db->delete('nl_accounts', 'mail = \'' . $form['mail'] . '\'');

			$content = combo_box($bool ? lang('newsletter', 'nl_unsubscribe_success') : lang('newsletter', 'nl_unsubscribe_error'), ROOT_DIR);
		}
		break;
	case 'activate':
		if (isset($modules->gen['mail']) && $validate->email($modules->gen['mail']) && isset($modules->gen['hash']) && preg_match('/^[a-f0-9]{32}+$/', $modules->gen['hash'])) {
			$mail = $modules->gen['mail'];
			$hash = $modules->gen['hash'];
		} else {
			redirect('errors/404');
		}

		if ($db->select('id', 'nl_accounts', 'mail = \'' . $mail . '\' AND hash = \'' . $db->escape($hash, 2) . '\'', 0, 0, 0, 1) != 1)
			$errors[] = lang('newsletter', 'nl_account_not_exists');

		if (isset($errors)) {
			combo_box($errors);
		} else {
			$bool = $db->update('nl_accounts', array('hash', ''), 'mail = \'' . $mail . '\' AND hash = \'' . $db->escape($hash, 2) . '\'');

			$content = combo_box($bool ? lang('newsletter', 'nl_activate_success') : lang('newsletter', 'nl_activate_error'), ROOT_DIR);
		}
		break;
	case 'activate_adm':
		$bool = !empty($modules->id) ? $db->update('nl_accounts', array('hash', ''), 'id = \'' . $modules->id . '\'') : false;

		$content = combo_box($bool ? lang('newsletter', 'nl_activate_success') : lang('newsletter', 'nl_activate_error'), uri('acp/newsletter'));
		break;
	case 'settings':
		$form = $_POST['form'];

		if (!$validate->email($form['mail']))
			$errors[] = lang('common', 'wrong_email_format');

		if (isset($errors)) {
			combo_box($errors);
		} else {
			$bool = $config->module('newsletter', $form);

			$content = combo_box($bool ? lang('newsletter', 'edit_success') : lang('newsletter', 'edit_error'), uri('acp/newsletter'));
		}
		break;
	case 'compose':
		$form = $_POST['form'];

		if (strlen($form['subject']) < 3)
			$errors[] = lang('newsletter', 'subject_to_short');
		if (strlen($form['text']) < 3)
			$errors[] = lang('newsletter', 'text_to_short');

		if (isset($errors)) {
			include 'modules/common/errors.php';
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
			$content = combo_box($bool ? lang('newsletter', 'compose_success') : lang('newsletter', 'compose_error'), uri('acp/newsletter'));
		}
		break;
	case 'delete':
		if (isset($_POST['entries']) && is_array($_POST['entries']))
			$entries = $_POST['entries'];
		elseif (isset($modules->gen['entries']) && preg_match('/^([\d|]+)$/', $modules->gen['entries']))
			$entries = $modules->gen['entries'];

		if (is_array($entries)) {
			$marked_entries = '';
			foreach ($entries as $entry) {
				$marked_entries.= $entry . '|';
			}
			$content = combo_box(lang('newsletter', 'confirm_delete'), uri('acp/newsletter/acp_list/action_delete/entries_' . $marked_entries), uri('acp/newsletter'));
		} elseif (preg_match('/^([\d|]+)$/', $entries) && isset($modules->gen['confirmed'])) {
			$marked_entries = explode('|', $entries);
			$bool = 0;
			foreach ($marked_entries as $entry) {
				if (!empty($entry) && $validate->is_number($entry) && $db->select('id', 'nl_accounts', 'id = \'' . $entry . '\'', 0, 0, 0, 1) == '1')
					$bool = $db->delete('nl_accounts', 'id = \'' . $entry . '\'');
			}
			$content = combo_box($bool ? lang('newsletter', 'delete_success') : lang('newsletter', 'delete_error'), uri('acp/newsletter'));
		} else {
			redirect('errors/404');
		}
		break;
	default:
		redirect('errors/404');
}
?>