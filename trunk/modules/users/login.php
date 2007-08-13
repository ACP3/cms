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

if (isset($_POST['submit'])) {
	$form = $_POST['form'];

	$user = $db->select('id, pwd, access', 'users', "name='" . $db->escape($form['name']) . "'");
	$auth = false;
	if (count($user) == 1 && !empty($user[0]['pwd'])) {
		// Schauen, ob Passwort in Datenbank schon sha1 ist
		$is_sha1 = strlen($user[0]['pwd']) == 53 ? true : false;

		// Hash für eingegebenes Passwort generieren
		$salt = substr($user[0]['pwd'], 41, 53);
		$pwd_hash = sha1($salt . sha1($form['pwd']));

		// Wenn Passwort als gesalzendes sha1 gefunden wurde, User authentifizieren
		if ($is_sha1 && substr($user[0]['pwd'], 0, 40) == $pwd_hash) {
			$auth = true;
		// Altes md5-Passwort zu gesalzenem sha1-Passwort umwandeln
		} elseif (!$is_sha1 && $user[0]['pwd'] == md5($form['pwd'])) {
			$salt = salt(12);
			$new_pwd = sha1($salt . sha1($form['pwd']));

			// SQL-Daten aktualisieren
			$bool = $db->update('users', array('pwd' => $new_pwd . ':' . $salt), 'id = \'' . $user[0]['id'] . '\'');
			$auth = $bool ? true : false;
		}
	}
	if ($auth) {
		// Ein Jahr oder eine Stunde...
		$expire = isset($_POST['remember']) ? 31104000 : 3600;
		$cookie_pwd = isset($new_pwd) ? $new_pwd : substr($user[0]['pwd'], 0, 40);

		setcookie('ACP3_AUTH', $db->escape($form['name']) . '|' . $cookie_pwd, time() + $expire, '/');

		session_start();
		$_SESSION['acp3_id'] = $user[0]['id'];
		$_SESSION['acp3_access'] = $user[0]['access'];

		redirect('acp/home');
	} else {
		$tpl->assign('error', lang('users', 'user_does_not_exists'));
	}
}
$content = $tpl->fetch('users/login.html');
?>