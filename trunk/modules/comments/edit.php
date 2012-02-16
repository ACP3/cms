<?php
/**
 * Comments
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (validate::isNumber($uri->id) === true && $db->countRows('*', 'comments', 'id = \'' . $uri->id . '\'') == 1) {
	$comment = $db->select('name, user_id, message, module', 'comments', 'id = \'' . $uri->id . '\'');

	$comment[0]['module'] = $db->escape($comment[0]['module'], 3);
	$breadcrumb->assign($lang->t($comment[0]['module'], $comment[0]['module']), $uri->route('acp/comments/adm_list/module_' . $comment[0]['module']))
			   ->assign($lang->t('comments', 'edit'));

	if (isset($_POST['form']) === true) {
		$form = $_POST['form'];

		if ((empty($comment[0]['user_id']) || validate::isNumber($comment[0]['user_id']) === false) && empty($form['name']))
			$errors[] = $lang->t('common', 'name_to_short');
		if (strlen($form['message']) < 3)
			$errors[] = $lang->t('common', 'message_to_short');

		if (isset($errors) === true) {
			$tpl->assign('error_msg', errorBox($errors));
		} elseif (validate::formToken() === false) {
			view::setContent(errorBox($lang->t('common', 'form_already_submitted')));
		} else {
			$update_values = array();
			$update_values['message'] = $db->escape($form['message']);
			if ((empty($comment[0]['user_id']) || validate::isNumber($comment[0]['user_id']) === false) && !empty($form['name'])) {
				$update_values['name'] = $db->escape($form['name']);
			}

			$bool = $db->update('comments', $update_values, 'id = \'' . $uri->id . '\'');

			$session->unsetFormToken();

			setRedirectMessage($bool !== false ? $lang->t('common', 'edit_success') : $lang->t('common', 'edit_error'), 'acp/comments/adm_list/module_' . $comment[0]['module']);
		}
	}
	if (isset($_POST['form']) === false || isset($errors) === true && is_array($errors) === true) {
		if (modules::check('emoticons', 'functions') === true) {
			require_once MODULES_DIR . 'emoticons/functions.php';

			// Emoticons im Formular anzeigen
			$tpl->assign('emoticons', emoticonsList());
		}

		$tpl->assign('form', isset($form) ? $form : $comment[0]);

		$session->generateFormToken();

		view::setContent(view::fetchTemplate('comments/edit.tpl'));
	}
} else {
	$uri->redirect('errors/404');
}
