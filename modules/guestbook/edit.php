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

if (!empty($modules->id) && $db->select('id', 'guestbook', 'id = \'' . $modules->id . '\'', 0, 0, 0, 1) == '1') {
	if (isset($_POST['submit'])) {
		$form = $_POST['form'];

		if (empty($form['name']))
			$errors[] = lang('common', 'name_to_short');
		if (strlen($form['message']) < 3)
			$errors[] = lang('common', 'message_to_short');

		if (isset($errors)) {
			combo_box($errors);
		} else {
			$update_values = array(
				'name' => $db->escape($form['name']),
				'message' => $db->escape($form['message']),
			);

			$bool = $db->update('guestbook', $update_values, 'id = \'' . $modules->id . '\'');

			$content = combo_box($bool ? lang('guestbook', 'edit_success') : lang('guestbook', 'edit_error'), uri('acp/guestbook'));
		}
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$guestbook = $db->select('name, message', 'guestbook', 'id = \'' . $modules->id . '\'');

		$tpl->assign('form', isset($form) ? $form : $guestbook[0]);

		$content = $tpl->fetch('guestbook/edit.html');
	}
} else {
	redirect('errors/404');
}
?>