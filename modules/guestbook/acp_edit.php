<?php
/**
 * Guestbook
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true &&
	ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'guestbook WHERE id = ?', array(ACP3_CMS::$uri->id)) == 1) {
	$settings = ACP3_Config::getSettings('guestbook');

	if (isset($_POST['submit']) === true) {
		if (empty($_POST['name']))
			$errors['name'] = ACP3_CMS::$lang->t('system', 'name_to_short');
		if (strlen($_POST['message']) < 3)
			$errors['message'] = ACP3_CMS::$lang->t('system', 'message_to_short');
		if ($settings['notify'] == 2 && (!isset($_POST['active']) || ($_POST['active'] != 0 && $_POST['active'] != 1)))
			$errors['notify'] = ACP3_CMS::$lang->t('guestbook', 'select_activate');

		if (isset($errors) === true) {
			ACP3_CMS::$view->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
		} else {
			$update_values = array(
				'name' => str_encode($_POST['name']),
				'message' => str_encode($_POST['message']),
				'active' => $settings['notify'] == 2 ? $_POST['active'] : 1,
			);

			$bool = ACP3_CMS::$db2->update(DB_PRE . 'guestbook', $update_values, array('id' => ACP3_CMS::$uri->id));

			ACP3_CMS::$session->unsetFormToken();

			setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/guestbook');
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$guestbook = ACP3_CMS::$db2->fetchAssoc('SELECT name, message, active FROM ' . DB_PRE . 'guestbook WHERE id = ?', array(ACP3_CMS::$uri->id));

		if (ACP3_Modules::check('emoticons', 'functions') === true && $settings['emoticons'] == 1) {
			require_once MODULES_DIR . 'emoticons/functions.php';

			//Emoticons im Formular anzeigen
			ACP3_CMS::$view->assign('emoticons', emoticonsList());
		}

		if ($settings['notify'] == 2) {
			$activate = array();
			$activate[0]['value'] = '1';
			$activate[0]['checked'] = selectEntry('active', '1', $guestbook['active'], 'checked');
			$activate[0]['lang'] = ACP3_CMS::$lang->t('system', 'yes');
			$activate[1]['value'] = '0';
			$activate[1]['checked'] = selectEntry('active', '0', $guestbook['active'], 'checked');
			$activate[1]['lang'] = ACP3_CMS::$lang->t('system', 'no');
			ACP3_CMS::$view->assign('activate', $activate);
		}

		ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : $guestbook);

		ACP3_CMS::$session->generateFormToken();

		ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('guestbook/acp_edit.tpl'));
	}
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}
