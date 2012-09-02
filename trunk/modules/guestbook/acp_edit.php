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

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true && ACP3_CMS::$db->countRows('*', 'guestbook', 'id = \'' . ACP3_CMS::$uri->id . '\'') == 1) {
	$settings = ACP3_Config::getSettings('guestbook');

	if (isset($_POST['submit']) === true) {
		if (empty($_POST['name']))
			$errors['name'] = ACP3_CMS::$lang->t('common', 'name_to_short');
		if (strlen($_POST['message']) < 3)
			$errors['message'] = ACP3_CMS::$lang->t('common', 'message_to_short');
		if ($settings['notify'] == 2 && (!isset($_POST['active']) || ($_POST['active'] != 0 && $_POST['active'] != 1)))
			$errors['notify'] = ACP3_CMS::$lang->t('guestbook', 'select_activate');

		if (isset($errors) === true) {
			ACP3_CMS::$view->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('common', 'form_already_submitted')));
		} else {
			$update_values = array(
				'name' => ACP3_CMS::$db->escape($_POST['name']),
				'message' => ACP3_CMS::$db->escape($_POST['message']),
				'active' => $settings['notify'] == 2 ? $_POST['active'] : 1,
			);

			$bool = ACP3_CMS::$db->update('guestbook', $update_values, 'id = \'' . ACP3_CMS::$uri->id . '\'');

			ACP3_CMS::$session->unsetFormToken();

			setRedirectMessage($bool, ACP3_CMS::$lang->t('common', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/guestbook');
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$guestbook = ACP3_CMS::$db->select('name, message, active', 'guestbook', 'id = \'' . ACP3_CMS::$uri->id . '\'');
		$guestbook[0]['name'] = ACP3_CMS::$db->escape($guestbook[0]['name'], 3);
		$guestbook[0]['message'] = ACP3_CMS::$db->escape($guestbook[0]['message'], 3);

		if (ACP3_Modules::check('emoticons', 'functions') === true && $settings['emoticons'] == 1) {
			require_once MODULES_DIR . 'emoticons/functions.php';

			//Emoticons im Formular anzeigen
			ACP3_CMS::$view->assign('emoticons', emoticonsList());
		}

		if ($settings['notify'] == 2) {
			$activate = array();
			$activate[0]['value'] = '1';
			$activate[0]['checked'] = selectEntry('active', '1', $guestbook[0]['active'], 'checked');
			$activate[0]['lang'] = ACP3_CMS::$lang->t('common', 'yes');
			$activate[1]['value'] = '0';
			$activate[1]['checked'] = selectEntry('active', '0', $guestbook[0]['active'], 'checked');
			$activate[1]['lang'] = ACP3_CMS::$lang->t('common', 'no');
			ACP3_CMS::$view->assign('activate', $activate);
		}

		ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : $guestbook[0]);

		ACP3_CMS::$session->generateFormToken();

		ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('guestbook/acp_edit.tpl'));
	}
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}
