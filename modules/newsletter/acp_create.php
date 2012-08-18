<?php
/**
 * Newsletter
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (isset($_POST['submit']) === true) {
	if (strlen($_POST['subject']) < 3)
		$errors['subject'] = $lang->t('newsletter', 'subject_to_short');
	if (strlen($_POST['text']) < 3)
		$errors['text'] = $lang->t('newsletter', 'text_to_short');

	if (isset($errors) === true) {
		$tpl->assign('error_msg', errorBox($errors));
	} elseif (ACP3_Validate::formToken() === false) {
		ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
	} else {
		$settings = ACP3_Config::getModuleSettings('newsletter');

		// Newsletter archivieren
		$insert_values = array(
			'id' => '',
			'date' => $date->getCurrentDateTime(),
			'subject' => $db->escape($_POST['subject']),
			'text' => $db->escape($_POST['text']),
			'status' => $_POST['test'] == 1 ? '0' : (int) $_POST['action'],
			'user_id' => $auth->getUserId(),
		);
		$bool = $db->insert('newsletter_archive', $insert_values);

		if ($_POST['action'] == 1 && $bool !== false) {
			$subject = $_POST['subject'];
			$body = $_POST['text'] . "\n-- \n" . html_entity_decode($db->escape($settings['mailsig'], 3), ENT_QUOTES, 'UTF-8');

			// Testnewsletter
			if ($_POST['test'] == 1) {
				$bool2 = generateEmail('', $settings['mail'], $settings['mail'], $subject, $body);
			// An alle versenden
			} else {
				$accounts = $db->select('mail', 'newsletter_accounts', 'hash = \'\'');
				$c_accounts = count($accounts);

				for ($i = 0; $i < $c_accounts; ++$i) {
					$bool2 = generateEmail('', $accounts[$i]['mail'], $settings['mail'], $subject, $body);
					if ($bool2 === false)
						break;
				}
			}
		}

		$session->unsetFormToken();

		if ($_POST['action'] == 0 && $bool !== false) {
			setRedirectMessage(true, $lang->t('newsletter', 'save_success'), 'acp/newsletter');
		} elseif ($_POST['action'] == 1 && $bool !== false && $bool2 === true) {
			setRedirectMessage($bool && $bool2, $lang->t('newsletter', 'compose_success'), 'acp/newsletter');
		} else {
			setRedirectMessage(false, $lang->t('newsletter', 'compose_save_error'), 'acp/newsletter');
		}
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	$tpl->assign('form', isset($_POST['submit']) ? $_POST : array('subject' => '', 'text' => ''));

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

	ACP3_View::setContent(ACP3_View::fetchTemplate('newsletter/acp_create.tpl'));
}