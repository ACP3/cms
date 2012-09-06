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

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true &&
	ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'newsletter_archive WHERE id = ?', array(ACP3_CMS::$uri->id)) == 1) {
	// Brotkrümelspur
	ACP3_CMS::$breadcrumb
	->append(ACP3_CMS::$lang->t('newsletter', 'newsletter'), ACP3_CMS::$uri->route('acp/newsletter'))
	->append(ACP3_CMS::$lang->t('newsletter', 'acp_edit'));

	if (isset($_POST['submit']) === true) {
		if (strlen($_POST['subject']) < 3)
			$errors['subject'] = ACP3_CMS::$lang->t('newsletter', 'subject_to_short');
		if (strlen($_POST['text']) < 3)
			$errors['text'] = ACP3_CMS::$lang->t('newsletter', 'text_to_short');

		if (isset($errors) === true) {
			ACP3_CMS::$view->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
		} else {
			$settings = ACP3_Config::getSettings('newsletter');

			// Newsletter archivieren
			$update_values = array(
				'date' => ACP3_CMS::$date->getCurrentDateTime(),
				'subject' => $_POST['subject'],
				'text' => $_POST['text'],
				'status' => $_POST['test'] == 1 ? '0' : (int) $_POST['action'],
				'user_id' => ACP3_CMS::$auth->getUserId(),
			);
			$bool = ACP3_CMS::$db2->update(DB_PRE . 'newsletter_archive', $update_values, array('id' => ACP3_CMS::$uri->id));

			if ($_POST['action'] == 1 && $bool !== false) {
				$subject = $_POST['subject'];
				$body = $_POST['text'] . "\n" . html_entity_decode($settings['mailsig'], ENT_QUOTES, 'UTF-8');

				// Testnewsletter
				if ($_POST['test'] == 1) {
					$bool2 = generateEmail('', $settings['mail'], $settings['mail'], $subject, $body);
				// An alle versenden
				} else {
					require_once MODULES_DIR . 'newsletter/functions.php';
					$bool2 = sendNewsletter($subject, $body, $settings['mail']);
				}
			}

			ACP3_CMS::$session->unsetFormToken();

			if ($_POST['action'] == 0 && $bool !== false) {
				setRedirectMessage(true, ACP3_CMS::$lang->t('newsletter', 'save_success'), 'acp/newsletter');
			} elseif ($_POST['action'] == 1 && $bool !== false && $bool2 === true) {
				setRedirectMessage($bool && $bool2, ACP3_CMS::$lang->t('newsletter', 'create_success'), 'acp/newsletter');
			} else {
				setRedirectMessage(false, ACP3_CMS::$lang->t('newsletter', 'create_save_error'), 'acp/newsletter');
			}
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$newsletter = ACP3_CMS::$db2->fetchAssoc('SELECT subject, text FROM ' . DB_PRE . 'newsletter_archive WHERE id = ?', array(ACP3_CMS::$uri->id));

		ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : $newsletter);

		$test = array();
		$test[0]['value'] = '1';
		$test[0]['checked'] = selectEntry('test', '1', '0', 'checked');
		$test[0]['lang'] = ACP3_CMS::$lang->t('system', 'yes');
		$test[1]['value'] = '0';
		$test[1]['checked'] = selectEntry('test', '0', '0', 'checked');
		$test[1]['lang'] = ACP3_CMS::$lang->t('system', 'no');
		ACP3_CMS::$view->assign('test', $test);

		$action = array();
		$action[0]['value'] = '1';
		$action[0]['checked'] = selectEntry('action', '1', '1', 'checked');
		$action[0]['lang'] = ACP3_CMS::$lang->t('newsletter', 'send_and_save');
		$action[1]['value'] = '0';
		$action[1]['checked'] = selectEntry('action', '0', '1', 'checked');
		$action[1]['lang'] = ACP3_CMS::$lang->t('newsletter', 'only_save');
		ACP3_CMS::$view->assign('action', $action);

		ACP3_CMS::$session->generateFormToken();

		ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('newsletter/acp_edit.tpl'));
	}
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}