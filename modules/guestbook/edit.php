<?php
/**
 * Guestbook
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (validate::isNumber($uri->id) && $db->countRows('*', 'guestbook', 'id = \'' . $uri->id . '\'') == '1') {
	$settings = config::output('guestbook');

	if (isset($_POST['form'])) {
		$form = $_POST['form'];

		if (empty($form['name']))
			$errors[] = $lang->t('common', 'name_to_short');
		if (strlen($form['message']) < 3)
			$errors[] = $lang->t('common', 'message_to_short');
		if ($settings['notify'] == 2 && (!isset($form['active']) || ($form['active'] != 0 && $form['active'] != 1)))
			$errors[] = $lang->t('guestbook', 'select_activate');

		if (isset($errors)) {
			$tpl->assign('error_msg', comboBox($errors));
		} else {
			$update_values = array(
				'name' => db::escape($form['name']),
				'message' => db::escape($form['message']),
				'active' => $settings['notify'] == 2 ? $form['active'] : 1,
			);

			$bool = $db->update('guestbook', $update_values, 'id = \'' . $uri->id . '\'');

			$content = comboBox($bool !== null ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), uri('acp/guestbook'));
		}
	}
	if (!isset($_POST['form']) || isset($errors) && is_array($errors)) {
		$guestbook = $db->select('name, message, active', 'guestbook', 'id = \'' . $uri->id . '\'');

		if (modules::check('emoticons', 'functions') == 1 && $settings['emoticons'] == 1) {
			require_once ACP3_ROOT . 'modules/emoticons/functions.php';

			//Emoticons im Formular anzeigen
			$tpl->assign('emoticons', emoticonsList());
		}

		if ($settings['notify'] == 2) {
			$activate[0]['value'] = '1';
			$activate[0]['checked'] = selectEntry('active', '1', $guestbook[0]['active'], 'checked');
			$activate[0]['lang'] = $lang->t('common', 'yes');
			$activate[1]['value'] = '0';
			$activate[1]['checked'] = selectEntry('active', '0', $guestbook[0]['active'], 'checked');
			$activate[1]['lang'] = $lang->t('common', 'no');
			$tpl->assign('activate', $activate);
		}

		$tpl->assign('form', isset($form) ? $form : $guestbook[0]);

		$content = modules::fetchTemplate('guestbook/edit.html');
	}
} else {
	redirect('errors/404');
}
