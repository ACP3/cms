<?php
/**
 * Users
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

// Falls der Benutzer schon eingeloggt ist, diesen zur Startseite weiterleiten
if ($auth->isUser() === true) {
	$uri->redirect(0, ROOT_DIR);
} elseif (isset($_POST['submit']) === true) {

	$result = $auth->login($_POST['nickname'], $_POST['pwd'], isset($_POST['remember']) ? 31104000 : 3600);
	if ($result == 1) {
		if (isset($_POST['redirect_uri'])) {
			$uri->redirect(base64_decode($_POST['redirect_uri']));
		} elseif (defined('IN_ADM')) {
			$uri->redirect($uri->redirect ? base64_decode($uri->redirect) : 'acp');
		} else {
			$uri->redirect(0, ROOT_DIR);
		}
	} else {
		$tpl->assign('error_msg', errorBox($lang->t('users', $result == -1 ? 'account_locked' : 'nickname_or_password_wrong')));
	}
}

ACP3_View::setContent(ACP3_view::fetchTemplate('users/login.tpl'));