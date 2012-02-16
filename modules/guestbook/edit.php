<?php
/**
 * Guestbook
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (validate::isNumber($uri->id) === true && $db->countRows('*', 'guestbook', 'id = \'' . $uri->id . '\'') == 1) {
	$settings = config::getModuleSettings('guestbook');

	if (isset($_POST['form']) === true) {
		$form = $_POST['form'];

		if (empty($form['name']))
			$errors[] = $lang->t('common', 'name_to_short');
		if (strlen($form['message']) < 3)
			$errors[] = $lang->t('common', 'message_to_short');
		if ($settings['notify'] == 2 && (!isset($form['active']) || ($form['active'] != 0 && $form['active'] != 1)))
			$errors[] = $lang->t('guestbook', 'select_activate');

		if (isset($errors) === true) {
			$tpl->assign('error_msg', errorBox($errors));
		} elseif (validate::formToken() === false) {
			view::setContent(errorBox($lang->t('common', 'form_already_submitted')));
		} else {
			$update_values = array(
				'name' => $db->escape($form['name']),
				'message' => $db->escape($form['message']),
				'active' => $settings['notify'] == 2 ? $form['active'] : 1,
			);

			$bool = $db->update('guestbook', $update_values, 'id = \'' . $uri->id . '\'');

			$session->unsetFormToken();

			setRedirectMessage($bool !== false ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), 'acp/guestbook');
		}
	}
	if (isset($_POST['form']) === false || isset($errors) === true && is_array($errors) === true) {
		$guestbook = $db->select('name, message, active', 'guestbook', 'id = \'' . $uri->id . '\'');
		$guestbook[0]['name'] = $db->escape($guestbook[0]['name'], 3);
		$guestbook[0]['message'] = $db->escape($guestbook[0]['message'], 3);

		if (modules::check('emoticons', 'functions') === true && $settings['emoticons'] == 1) {
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

		$tpl->assign('form', isset($form) ? $form : $guestbook[0]);

		$session->generateFormToken();

		view::setContent(view::fetchTemplate('guestbook/edit.tpl'));
	}
} else {
	$uri->redirect('errors/404');
}
