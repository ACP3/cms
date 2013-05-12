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
	if (strlen($_POST['title']) < 3)
		$errors['title'] = ACP3\CMS::$injector['Lang']->t('newsletter', 'subject_to_short');
	if (strlen($_POST['text']) < 3)
		$errors['text'] = ACP3\CMS::$injector['Lang']->t('newsletter', 'text_to_short');

	if (isset($errors) === true) {
		ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
	} elseif (ACP3\Core\Validate::formToken() === false) {
		ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted')));
	} else {
		$settings = ACP3\Core\Config::getSettings('newsletter');

		// Newsletter archivieren
		$insert_values = array(
			'id' => '',
			'date' => ACP3\CMS::$injector['Date']->getCurrentDateTime(),
			'title' => ACP3\Core\Functions::str_encode($_POST['title']),
			'text' => ACP3\Core\Functions::str_encode($_POST['text'], true),
			'status' => $_POST['test'] == 1 ? '0' : (int) $_POST['action'],
			'user_id' => ACP3\CMS::$injector['Auth']->getUserId(),
		);
		$bool = ACP3\CMS::$injector['Db']->insert(DB_PRE . 'newsletters', $insert_values);

		if ($_POST['action'] == 1 && $bool !== false) {
			$subject = ACP3\Core\Functions::str_encode($_POST['title'], true);
			$body = ACP3\Core\Functions::str_encode($_POST['text'], true) . "\n-- \n" . html_entity_decode($settings['mailsig'], ENT_QUOTES, 'UTF-8');

			// Testnewsletter
			if ($_POST['test'] == 1) {
				$bool2 = generateEmail('', $settings['mail'], $settings['mail'], $subject, $body);
			// An alle versenden
			} else {
				require_once MODULES_DIR . 'newsletter/functions.php';
				$bool2 = sendNewsletter($subject, $body, $settings['mail']);
			}
		}

		ACP3\CMS::$injector['Session']->unsetFormToken();

		if ($_POST['action'] == 0 && $bool !== false) {
			ACP3\Core\Functions::setRedirectMessage(true, ACP3\CMS::$injector['Lang']->t('newsletter', 'save_success'), 'acp/newsletter');
		} elseif ($_POST['action'] == 1 && $bool !== false && $bool2 === true) {
			ACP3\Core\Functions::setRedirectMessage($bool && $bool2, ACP3\CMS::$injector['Lang']->t('newsletter', 'create_success'), 'acp/newsletter');
		} else {
			ACP3\Core\Functions::setRedirectMessage(false, ACP3\CMS::$injector['Lang']->t('newsletter', 'create_save_error'), 'acp/newsletter');
		}
	}
}
if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
	ACP3\CMS::$injector['View']->assign('form', isset($_POST['submit']) ? $_POST : array('title' => '', 'text' => ''));

	$lang_test = array(ACP3\CMS::$injector['Lang']->t('system', 'yes'), ACP3\CMS::$injector['Lang']->t('system', 'no'));
	ACP3\CMS::$injector['View']->assign('test', ACP3\Core\Functions::selectGenerator('test', array(1, 0), $lang_test, 0, 'checked'));

	$lang_action = array(ACP3\CMS::$injector['Lang']->t('newsletter', 'send_and_save'), ACP3\CMS::$injector['Lang']->t('newsletter', 'only_save'));
	ACP3\CMS::$injector['View']->assign('action', ACP3\Core\Functions::selectGenerator('action', array(1, 0), $lang_action, 1, 'checked'));

	ACP3\CMS::$injector['Session']->generateFormToken();
}