<?php
/**
 * Newsletter
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['form']) === true) {
	$form = $_POST['form'];

	if (strlen($form['subject']) < 3)
		$errors[] = $lang->t('newsletter', 'subject_to_short');
	if (strlen($form['text']) < 3)
		$errors[] = $lang->t('newsletter', 'text_to_short');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', comboBox($errors));
	} elseif (!validate::formToken()) {
		view::setContent(comboBox($lang->t('common', 'form_already_submitted')));
	} else {
		$settings = config::getModuleSettings('newsletter');

		// Newsletter archivieren
		$insert_values = array(
			'id' => '',
			'date' => $date->timestamp(),
			'subject' => $db->escape($form['subject']),
			'text' => $db->escape($form['text']),
			'status' => $form['test'] == '1' ? '0' : (int) $form['action'],
			'user_id' => $auth->getUserId(),
		);
		$bool = $db->insert('newsletter_archive', $insert_values);

		if ($form['action'] == '1' && $bool) {
			$subject = $form['subject'];
			$body = $form['text'] . "\n" . html_entity_decode($db->escape($settings['mailsig'], 3), ENT_QUOTES, 'UTF-8');

			// Testnewsletter
			if ($form['test'] == '1') {
				$bool2 = generateEmail('', $settings['mail'], $settings['mail'], $subject, $body);
			// An alle versenden
			} else {
				$accounts = $db->select('mail', 'newsletter_accounts', 'hash = \'\'');
				$c_accounts = count($accounts);

				for ($i = 0; $i < $c_accounts; ++$i) {
					$bool2 = generateEmail('', $accounts[$i]['mail'], $settings['mail'], $subject, $body);
					if ($bool2 !== true)
						break;
				}
			}
		}

		$session->unsetFormToken();

		if ($form['action'] == '0' && $bool) {
			view::setContent(comboBox($lang->t('newsletter', 'save_success'), $uri->route('acp/newsletter')));
		} elseif ($form['action'] == '1' && $bool && $bool2 === true) {
			view::setContent(comboBox($lang->t('newsletter', 'compose_success'), $uri->route('acp/newsletter')));
		} else {
			view::setContent(comboBox($lang->t('newsletter', 'compose_save_error'), $uri->route('acp/newsletter')));
		}
	}
}
if (isset($_POST['form']) === false || isset($errors) === true && is_array($errors) === true) {
	$tpl->assign('form', isset($form) ? $form : array('subject' => '', 'text' => ''));

	$test = array();
	$test[0]['value'] = '1';
	$test[0]['checked'] = selectEntry('test', '1', '0', 'checked');
	$test[0]['lang'] = $lang->t('common', 'yes');
	$test[1]['value'] = '0';
	$test[1]['checked'] = selectEntry('test', '0', '0', 'checked');
	$test[1]['lang'] = $lang->t('common', 'no');
	$tpl->assign('test', $test);

	$action = array();
	$action[0]['value'] = '1';
	$action[0]['checked'] = selectEntry('action', '1', '1', 'checked');
	$action[0]['lang'] = $lang->t('newsletter', 'send_and_save');
	$action[1]['value'] = '0';
	$action[1]['checked'] = selectEntry('action', '0', '1', 'checked');
	$action[1]['lang'] = $lang->t('newsletter', 'only_save');
	$tpl->assign('action', $action);

	$session->generateFormToken();

	view::setContent(view::fetchTemplate('newsletter/compose.tpl'));
}