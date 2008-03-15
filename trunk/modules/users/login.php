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

	$user = $db->select('id, pwd', 'users', 'nickname = \'' . $db->escape($form['nickname']) . '\'');
	$isUser = false;

	if (count($user) == '1') {
		// Passwort aus Datenbank
		$db_hash = substr($user[0]['pwd'], 0, 40);

		// Hash für eingegebenes Passwort generieren
		$salt = substr($user[0]['pwd'], 41, 53);
		$form_pwd_hash = sha1($salt . sha1($form['pwd']));

		// Wenn beide Hash Werte gleich sind, Benutzer authentifizieren
		if ($db_hash == $form_pwd_hash) {
			$isUser = true;
		}
	}
	if ($isUser) {
		// Werte für den Cookie setzen
		$cookie['value'] = base64_encode($db->escape($form['nickname']) . '|' . $db_hash);
		$cookie['expires'] = time() + (isset($_POST['remember']) ? 31104000 : 3600);
		setcookie('ACP3_AUTH', $cookie['value'], $cookie['expires'], '/');

		if (isset($form['redirect_uri'])) {
			redirect(0, base64_decode($form['redirect_uri']));
		} else {
			redirect(0, ROOT_DIR);
		}
	} else {
		combo_box(array(lang('users', 'nickname_or_password_wrong')));
	}
}
$content = $tpl->fetch('users/login.html');
?>