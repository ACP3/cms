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

if (ACP3_Validate::isNumber(ACP3_CMS::$uri->id) === true &&
	ACP3_CMS::$db2->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'comments WHERE id = ?', array(ACP3_CMS::$uri->id)) == 1) {
	$comment = ACP3_CMS::$db2->fetchAssoc('SELECT c.name, c.user_id, c.message, c.module_id, m.name AS module FROM ' . DB_PRE . 'comments AS c JOIN ' . DB_PRE . 'modules AS m ON(m.id = c.module_id) WHERE c.id = ?', array(ACP3_CMS::$uri->id));

	ACP3_CMS::$breadcrumb
	->append(ACP3_CMS::$lang->t($comment['module'], $comment['module']), ACP3_CMS::$uri->route('acp/comments/list_comments/id_' . $comment['module_id']))
	->append(ACP3_CMS::$lang->t('comments', 'acp_edit'));

	if (isset($_POST['submit']) === true) {
		if ((empty($comment['user_id']) || ACP3_Validate::isNumber($comment['user_id']) === false) && empty($_POST['name']))
			$errors['name'] = ACP3_CMS::$lang->t('system', 'name_to_short');
		if (strlen($_POST['message']) < 3)
			$errors['message'] = ACP3_CMS::$lang->t('system', 'message_to_short');

		if (isset($errors) === true) {
			ACP3_CMS::$view->assign('error_msg', errorBox($errors));
		} elseif (ACP3_Validate::formToken() === false) {
			ACP3_CMS::setContent(errorBox(ACP3_CMS::$lang->t('system', 'form_already_submitted')));
		} else {
			$update_values = array();
			$update_values['message'] = $_POST['message'];
			if ((empty($comment['user_id']) || ACP3_Validate::isNumber($comment['user_id']) === false) && !empty($_POST['name'])) {
				$update_values['name'] = $_POST['name'];
			}

			$bool = ACP3_CMS::$db2->update(DB_PRE . 'comments', $update_values, array('id' => ACP3_CMS::$uri->id));

			ACP3_CMS::$session->unsetFormToken();

			setRedirectMessage($bool, ACP3_CMS::$lang->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/comments/list_comments/id_' . $comment['module_id']);
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		if (ACP3_Modules::check('emoticons', 'functions') === true) {
			require_once MODULES_DIR . 'emoticons/functions.php';

			// Emoticons im Formular anzeigen
			ACP3_CMS::$view->assign('emoticons', emoticonsList());
		}

		ACP3_CMS::$view->assign('form', isset($_POST['submit']) ? $_POST : $comment);
		ACP3_CMS::$view->assign('module_id', (int) $comment['module_id']);

		ACP3_CMS::$session->generateFormToken();

		ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('comments/acp_edit.tpl'));
	}
} else {
	ACP3_CMS::$uri->redirect('errors/404');
}
