<?php
/**
 * Users
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3') && !defined('IN_ADM'))
	exit;

// Falls der Benutzer schon eingeloggt ist, diesen zur Startseite weiterleiten
if ($auth->isUser()) {
	redirect(0, ROOT_DIR);
} elseif (isset($_POST['submit'])) {
	$form = $_POST['form'];

	$user = $db->select('id, pwd, login_errors', 'users', 'nickname = \'' . $db->escape($form['nickname']) . '\' AND login_errors < 3');
	$isUser = false;
	$lock = false;

	if (count($user) == 1) {
		// Passwort aus Datenbank
		$db_hash = substr($user[0]['pwd'], 0, 40);

		// Hash für eingegebenes Passwort generieren
		$salt = substr($user[0]['pwd'], 41, 53);
		$form_pwd_hash = sha1($salt . sha1($form['pwd']));

		// Wenn beide Hashwerte gleich sind, Benutzer authentifizieren
		if ($db_hash === $form_pwd_hash) {
			$isUser = true;
			// Login-Fehler zurücksetzen
			if ($user[0]['login_errors'] > 0)
				$db->update('users', array('login_errors' => 0), 'id = \'' . $user[0]['id'] . '\'');
		// Beim dritten falschen Login den Account sperren
		} else {
			$l_errors = $user[0]['login_errors'] + 1;
			$db->update('users', array('login_errors' => $l_errors), 'id = \'' . $user[0]['id'] . '\'');
			if ($l_errors == 3) {
				$lock = true;
			}
		}
	}
	if ($isUser) {
		// Cookie setzen
		$auth->setCookie($form['nickname'], $db_hash, isset($_POST['remember']) ? 31104000 : 3600);

		if (isset($form['redirect_uri'])) {
			redirect(0, base64_decode($form['redirect_uri']));
		} elseif (defined('IN_ADM')) {
			redirect('acp');
		} else {
			redirect(0, ROOT_DIR);
		}
	} else {
		$error[] = $lang->t('users', $lock ? 'account_locked' : 'nickname_or_password_wrong_or_account_locked');
		$tpl->assign('error_msg', comboBox($error));
	}
}
$content = $tpl->fetch('users/login.html');
?>