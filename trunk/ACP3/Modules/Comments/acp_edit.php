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

if (ACP3\Core\Validate::isNumber(ACP3\CMS::$injector['URI']->id) === true &&
	ACP3\CMS::$injector['Db']->fetchColumn('SELECT COUNT(*) FROM ' . DB_PRE . 'comments WHERE id = ?', array(ACP3\CMS::$injector['URI']->id)) == 1) {
	$comment = ACP3\CMS::$injector['Db']->fetchAssoc('SELECT c.name, c.user_id, c.message, c.module_id, m.name AS module FROM ' . DB_PRE . 'comments AS c JOIN ' . DB_PRE . 'modules AS m ON(m.id = c.module_id) WHERE c.id = ?', array(ACP3\CMS::$injector['URI']->id));

	ACP3\CMS::$injector['Breadcrumb']
	->append(ACP3\CMS::$injector['Lang']->t($comment['module'], $comment['module']), ACP3\CMS::$injector['URI']->route('acp/comments/list_comments/id_' . $comment['module_id']))
	->append(ACP3\CMS::$injector['Lang']->t('comments', 'acp_edit'));

	if (isset($_POST['submit']) === true) {
		if ((empty($comment['user_id']) || ACP3\Core\Validate::isNumber($comment['user_id']) === false) && empty($_POST['name']))
			$errors['name'] = ACP3\CMS::$injector['Lang']->t('system', 'name_to_short');
		if (strlen($_POST['message']) < 3)
			$errors['message'] = ACP3\CMS::$injector['Lang']->t('system', 'message_to_short');

		if (isset($errors) === true) {
			ACP3\CMS::$injector['View']->assign('error_msg', Core\Functions::errorBox($errors));
		} elseif (ACP3\Core\Validate::formToken() === false) {
			ACP3\CMS::$injector['View']->setContent(Core\Functions::errorBox(ACP3\CMS::$injector['Lang']->t('system', 'form_already_submitted')));
		} else {
			$update_values = array();
			$update_values['message'] = ACP3\Core\Functions::str_encode($_POST['message']);
			if ((empty($comment['user_id']) || ACP3\Core\Validate::isNumber($comment['user_id']) === false) && !empty($_POST['name'])) {
				$update_values['name'] = ACP3\Core\Functions::str_encode($_POST['name']);
			}

			$bool = ACP3\CMS::$injector['Db']->update(DB_PRE . 'comments', $update_values, array('id' => ACP3\CMS::$injector['URI']->id));

			ACP3\CMS::$injector['Session']->unsetFormToken();

			ACP3\Core\Functions::setRedirectMessage($bool, ACP3\CMS::$injector['Lang']->t('system', $bool !== false ? 'edit_success' : 'edit_error'), 'acp/comments/list_comments/id_' . $comment['module_id']);
		}
	}
	if (isset($_POST['submit']) === false || isset($errors) === true && is_array($errors) === true) {
		if (ACP3\Core\Modules::check('emoticons', 'functions') === true) {
			require_once MODULES_DIR . 'emoticons/functions.php';

			// Emoticons im Formular anzeigen
			ACP3\CMS::$injector['View']->assign('emoticons', emoticonsList());
		}

		ACP3\CMS::$injector['View']->assign('form', isset($_POST['submit']) ? $_POST : $comment);
		ACP3\CMS::$injector['View']->assign('module_id', (int) $comment['module_id']);

		ACP3\CMS::$injector['Session']->generateFormToken();
	}
} else {
	ACP3\CMS::$injector['URI']->redirect('errors/404');
}
