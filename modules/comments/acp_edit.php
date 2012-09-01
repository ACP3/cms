<?php
/**
 * Comments
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

if (ACP3_Validate::isNumber($uri->id) === true && $db->countRows('*', 'comments', 'id = \'' . $uri->id . '\'') == 1) {
	$comment = $db->query('SELECT c.name, c.user_id, c.message, c.module_id, m.name AS module FROM {pre}comments AS c JOIN {pre}modules AS m ON(m.id = c.module_id) WHERE c.id = \'' . $uri->id . '\'');

	$comment[0]['module'] = $db->escape($comment[0]['module'], 3);
	$breadcrumb->append($lang->t($comment[0]['module'], $comment[0]['module']), $uri->route('acp/comments/list_comments/id_' . $comment[0]['module_id']))
			   ->append($lang->t('comments', 'acp_edit'));

	if (isset($_POST['submit']) === true) {
		if ((empty($comment[0]['user_id']) || ACP3_Validate::isNumber($comment[0]['user_id']) === false) && empty($_POST['name']))
			$errors['name'] = $lang->t('common', 'name_to_short');
		if (strlen($_POST['message']) < 3)
			$errors['message'] = $lang->t('common', 'message_to_short');

		if (isset($errors) === true) {
			$tpl->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_View::setContent(errorBox($lang->t('common', 'form_already_submitted')));
		} else {
			$update_values = array();
			$update_values['message'] = $db->escape($_POST['message']);
			if ((empty($comment[0]['user_id']) || ACP3_Validate::isNumber($comment[0]['user_id']) === false) && !empty($_POST['name'])) {
				$update_values['name'] = $db->escape($_POST['name']);
			}

			$bool = $db->update('comments', $update_values, 'id = \'' . $uri->id . '\'');

			$session->unsetFormToken();

			setRedirectMessage($bool, $lang->t('common', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/comments/list_comments/id_' . $comment[0]['module_id']);
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		if (ACP3_Modules::check('emoticons', 'functions') === true) {
			require_once MODULES_DIR . 'emoticons/functions.php';

			// Emoticons im Formular anzeigen
			$tpl->assign('emoticons', emoticonsList());
		}

		$tpl->assign('form', isset($_POST['submit']) ? $_POST : $comment[0]);
		$tpl->assign('module_id', (int) $comment[0]['module_id']);

		$session->generateFormToken();

		ACP3_View::setContent(ACP3_View::fetchTemplate('comments/acp_edit.tpl'));
	}
} else {
	$uri->redirect('errors/404');
}
