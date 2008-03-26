<?php
/**
 * Comments
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;

if (!empty($modules->id) && $db->select('id', 'comments', 'id = \'' . $modules->id . '\'', 0, 0, 0, 1) == '1') {
	$comment = $db->select('name, message, module', 'comments', 'id = \'' . $modules->id . '\'');

	$comment[0]['module'] = $db->escape($comment[0]['module'], 3);
	$breadcrumb->assign(lang('common', 'acp'), uri('acp'));
	$breadcrumb->assign(lang('comments', 'comments'), uri('acp/comments'));
	$breadcrumb->assign(lang($comment[0]['module'], $comment[0]['module']), uri('acp/comments/adm_list/module_' . $comment[0]['module']));
	$breadcrumb->assign(lang('comments', 'edit'));

	if (isset($_POST['submit'])) {
		$form = $_POST['form'];

		if (empty($form['name']))
			$errors[] = lang('common', 'name_to_short');
		if (strlen($form['message']) < 3)
			$errors[] = lang('common', 'message_to_short');

		if (isset($errors)) {
			$tpl->assign('error_msg', comboBox($errors));
		} else {
			$update_values = array(
				'name' => $db->escape($form['name']),
				'message' => $db->escape($form['message']),
			);

			$bool = $db->update('comments', $update_values, 'id = \'' . $modules->id . '\'');

			$content = comboBox($bool ? lang('comments', 'edit_success') : lang('comments', 'edit_error'), uri('acp/comments'));
		}
	}
	if (!isset($_POST['submit']) || isset($errors) && is_array($errors)) {
		$tpl->assign('form', isset($form) ? $form : $comment[0]);

		$content = $tpl->fetch('comments/edit.html');
	}
} else {
	redirect('errors/404');
}
?>