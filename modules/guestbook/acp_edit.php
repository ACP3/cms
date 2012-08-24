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

if (ACP3_Validate::isNumber($uri->id) === true && $db->countRows('*', 'guestbook', 'id = \'' . $uri->id . '\'') == 1) {
	$settings = ACP3_Config::getSettings('guestbook');

	if (isset($_POST['submit']) === true) {
		if (empty($_POST['name']))
			$errors['name'] = $lang->t('common', 'name_to_short');
		if (strlen($_POST['message']) < 3)
			$errors['message'] = $lang->t('common', 'message_to_short');
		if ($settings['notify'] == 2 && (!isset($_POST['active']) || ($_POST['active'] != 0 && $_POST['active'] != 1)))
			$errors['notify'] = $lang->t('guestbook', 'select_activate');

		if (isset($errors) === true) {
			$tpl->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
		} else {
			$update_values = array(
				'name' => $db->escape($_POST['name']),
				'message' => $db->escape($_POST['message']),
				'active' => $settings['notify'] == 2 ? $_POST['active'] : 1,
			);

			$bool = $db->update('guestbook', $update_values, 'id = \'' . $uri->id . '\'');

			$session->unsetFormToken();

			setRedirectMessage($bool, $lang->t('common', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/guestbook');
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		$guestbook = $db->select('name, message, active', 'guestbook', 'id = \'' . $uri->id . '\'');
		$guestbook[0]['name'] = $db->escape($guestbook[0]['name'], 3);
		$guestbook[0]['message'] = $db->escape($guestbook[0]['message'], 3);

		if (ACP3_Modules::check('emoticons', 'functions') === true && $settings['emoticons'] == 1) {
			require_once MODULES_DIR . 'emoticons/functions.php';

			//Emoticons im Formular anzeigen
			$tpl->assign('emoticons', emoticonsList());
		}

		if ($settings['notify'] == 2) {
			$activate = array();
			$activate[0]['value'] = '1';
			$activate[0]['checked'] = selectEntry('active', '1', $guestbook[0]['active'], 'checked');
			$activate[0]['lang'] = $lang->t('common', 'yes');
			$activate[1]['value'] = '0';
			$activate[1]['checked'] = selectEntry('active', '0', $guestbook[0]['active'], 'checked');
			$activate[1]['lang'] = $lang->t('common', 'no');
			$tpl->assign('activate', $activate);
		}

		$tpl->assign('form', isset($_POST['submit']) ? $_POST : $guestbook[0]);

		$session->generateFormToken();

		ACP3_View::setContent(ACP3_View::fetchTemplate('guestbook/acp_edit.tpl'));
	}
} else {
	$uri->redirect('errors/404');
}
