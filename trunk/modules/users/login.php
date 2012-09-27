<?php
/**
 * Users
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ACP3') === false)
	exit;

// Falls der Benutzer schon eingeloggt ist, diesen zur Startseite weiterleiten
if (ACP3_CMS::$auth->isUser() === true) {
	ACP3_CMS::$uri->redirect(0, ROOT_DIR);
} elseif (isset($_POST['submit']) === true) {
	$result = ACP3_CMS::$auth->login(str_encode($_POST['nickname']), $_POST['pwd'], isset($_POST['remember']) ? 31104000 : 3600);
	if ($result == 1) {
		if (ACP3_CMS::$uri->redirect) {
			ACP3_CMS::$uri->redirect(base64_decode(ACP3_CMS::$uri->redirect));
		} else {
			ACP3_CMS::$uri->redirect(0, ROOT_DIR);
		}
	} else {
		ACP3_CMS::$view->assign('error_msg', errorBox(ACP3_CMS::$lang->t('users', $result == -1 ? 'account_locked' : 'nickname_or_password_wrong')));
	}
}

ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('users/login.tpl'));