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
		$errors[] = $lang->t('newsletter', 'subject_to_short');
	if (strlen($form['text']) < 3)
		$errors[] = $lang->t('newsletter', 'text_to_short');

	if (isset($errors)) {
		$tpl->assign('error_msg', comboBox($errors));
	} else {
		$settings = config::output('newsletter');

		// Newsletter archivieren
		$insert_values = array(
			'id' => '',
			'date' => dateAligned(2, time()),
			'subject' => $db->escape($form['subject']),
			'text' => $db->escape($form['text']),
			'status' => (int) $form['action'],
		);
		$bool = $db->insert('newsletter_archive', $insert_values);

		if ($form['action'] == '1' && $bool) {
			// Testnewsletter
			if ($form['test'] == '1') {
				$bool2 = @mail($settings['mail'], $db->escape($form['subject']), $db->escape($form['text']) . $settings['mailsig'], 'FROM:' . $settings['mail'] . "\r\n" . 'Content-Type: text/plain; charset: UTF-8');
			// An alle versenden
			} else {
				$accounts = $db->select('mail', 'newsletter_accounts', 'hash = \'\'');
				$c_accounts = count($accounts);

				for ($i = 0; $i < $c_accounts; ++$i) {
					$bool2 = @mail($accounts[$i]['mail'], $db->escape($form['subject']), $db->escape($form['text']) . $settings['mailsig'], 'FROM:' . $settings['mail'] . "\r\n" . 'Content-Type: text/plain; charset: UTF-8');
					if (!$bool2)
						break;
				}
			}
		}
		if ($form['action'] == '0' && $bool) {
			$content = comboBox($lang->t('newsletter', 'save_success'), uri('acp/newsletter'));
		} elseif ($form['action'] == '1' && $bool && $bool2) {
			$content = comboBox($lang->t('newsletter', 'compose_success'), uri('acp/newsletter'));
		} else {
			$content = comboBox($lang->t('newsletter', 'compose_save_error'), uri('acp/newsletter'));
		}
	}
}
if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
	$tpl->assign('form', isset($form) ? $form : array('subject' => '', 'text' => ''));

	$test[0]['value'] = '1';
	$test[0]['checked'] = selectEntry('test', '1', '0', 'checked');
	$test[0]['lang'] = $lang->t('common', 'yes');
	$test[1]['value'] = '0';
	$test[1]['checked'] = selectEntry('test', '0', '0', 'checked');
	$test[1]['lang'] = $lang->t('common', 'no');
	$tpl->assign('test', $test);

	$action[0]['value'] = '1';
	$action[0]['checked'] = selectEntry('action', '1', '1', 'checked');
	$action[0]['lang'] = $lang->t('newsletter', 'send_and_save');
	$action[1]['value'] = '0';
	$action[1]['checked'] = selectEntry('action', '0', '1', 'checked');
	$action[1]['lang'] = $lang->t('newsletter', 'only_save');
	$tpl->assign('action', $action);

	$content = $tpl->fetch('newsletter/compose.html');
}
?>