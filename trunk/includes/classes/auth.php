<?php
/**
 * Authentification
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
/**
 * Authentifiziert den Benutzer
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
class auth
{
	/**
	 * User oder nicht
	 *
	 * @var boolean
	 */
	private $isUser = false;
	/**
	 * Einträge pro Seite
	 *
	 * @var integer
	 */
	 public $entries = CONFIG_ENTRIES;

	/**
	 * Findet heraus, falls der ACP3_AUTH Cookie gesetzt ist, ob der
	 * Seitenbesucher auch wirklich ein registrierter Benutzer des ACP3 ist
	 */
	function __construct()
	{
		if (isset($_COOKIE['ACP3_AUTH'])) {
			global $db;

			$cookie = base64_decode($_COOKIE['ACP3_AUTH']);
			$cookie_arr = explode('|', $cookie);

			$user_check = $db->select('id, pwd, entries', 'users', 'nickname = \'' . $db->escape($cookie_arr[0]) . '\' AND login_errors < 3');
			if (count($user_check) == 1) {
				$db_password = substr($user_check[0]['pwd'], 0, 40);
				if ($db_password == $cookie_arr[1]) {
					$this->isUser = true;
					$this->entries = (int) $user_check[0]['entries'];
					define('USER_ID', $user_check[0]['id']);
				}
			}
			if (!$this->isUser) {
				$this->logout();

				redirect(0, ROOT_DIR);
			}
		}
	}
	/**
	 * Gibt ein Array mit den angeforderten Daten eines Benutzers zurück
	 *
	 * @param integer $user_id
	 * 	Der angeforderte Benutzer
	 * @return mixed
	 */
	public function getUserInfo($user_id = '')
	{
		if (empty($user_id) && $this->isUser()) {
			$user_id = USER_ID;
		}
		if (validate::isNumber($user_id)) {
			static $user_info = array();

			if (empty($user_info[$user_id])) {
				global $auth, $db, $lang;

				$info = $db->select('nickname, access, realname, gender, birthday, birthday_format, mail, website, icq, msn, skype, time_zone, dst, language, draft', 'users', 'id = \'' . $user_id . '\'');
				if (!empty($info)) {
					$pos = strrpos($info[0]['realname'], ':');
					$info[0]['realname_display'] = substr($info[0]['realname'], $pos + 1);
					$info[0]['realname'] = substr($info[0]['realname'], 0, $pos);
					$pos = strrpos($info[0]['gender'], ':');
					$info[0]['gender_display'] = substr($info[0]['gender'], $pos + 1);
					$info[0]['gender'] = substr($info[0]['gender'], 0, $pos);
					$pos = strrpos($info[0]['birthday'], ':');
					$info[0]['birthday_display'] = substr($info[0]['birthday'], $pos + 1);
					$info[0]['birthday'] = substr($info[0]['birthday'], 0, $pos);
					$pos = strrpos($info[0]['mail'], ':');
					$info[0]['mail_display'] = substr($info[0]['mail'], $pos + 1);
					$info[0]['mail'] = substr($info[0]['mail'], 0, $pos);
					$pos = strrpos($info[0]['website'], ':');
					$info[0]['website_display'] = substr($info[0]['website'], $pos + 1);
					$info[0]['website'] = $db->escape(substr($info[0]['website'], 0, $pos), 3);
					$pos = strrpos($info[0]['icq'], ':');
					$info[0]['icq_display'] = substr($info[0]['icq'], $pos + 1);
					$info[0]['icq'] = substr($info[0]['icq'], 0, $pos);
					$pos = strrpos($info[0]['msn'], ':');
					$info[0]['msn_display'] = substr($info[0]['msn'], $pos + 1);
					$info[0]['msn'] = substr($info[0]['msn'], 0, $pos);
					$pos = strrpos($info[0]['skype'], ':');
					$info[0]['skype_display'] = substr($info[0]['skype'], $pos + 1);
					$info[0]['skype'] = substr($info[0]['skype'], 0, $pos);
					$user_info[$user_id] = $info[0];
				}
			}

			return !empty($user_info[$user_id]) ? $user_info[$user_id] : false;
		}
		return false;
	}
	/**
	 * Gibt den Status von $isUser zurück
	 *
	 * @return boolean
	 */
	public function isUser()
	{
		return $this->isUser && defined('USER_ID') && validate::isNumber(USER_ID) ? true : false;
	}
	/**
	 * Loggt einen User ein
	 *
	 * @param string $username
	 *	Der zu verwendente Username
	 * @param string $password
	 *	Das zu verwendente Passwort
	 * @param integer $expiry
	 *	Gibt die Zeit in Sekunden an, wie lange der User eingeloggt bleiben soll
	 * @return integer
	 */
	public function login($username, $password, $expiry)
	{
		global $db;

		$user = $db->select('id, pwd, login_errors', 'users', 'nickname = \'' . $db->escape($username) . '\'');
		$lock = false;

		if (count($user) == 1) {
			// Useraccount ist gesperrt
			if ($user[0]['login_errors'] >= 3) {
				return -1;
			}

			// Passwort aus Datenbank
			$db_hash = substr($user[0]['pwd'], 0, 40);

			// Hash für eingegebenes Passwort generieren
			$salt = substr($user[0]['pwd'], 41, 53);
			$form_pwd_hash = genSaltedPassword($salt, $password);

			// Wenn beide Hashwerte gleich sind, Benutzer authentifizieren
			if ($db_hash === $form_pwd_hash) {
				$isUser = true;
				// Login-Fehler zurücksetzen
				if ($user[0]['login_errors'] > 0)
					$db->update('users', array('login_errors' => 0), 'id = \'' . $user[0]['id'] . '\'');

				$this->setCookie($username, $db_hash, $expiry);
				$this->isUser = true;
				define('USER_ID', $user[0]['id']);

				return 1;
			// Beim dritten falschen Login den Account sperren
			} else {
				$login_errors = $user[0]['login_errors'] + 1;
				$db->update('users', array('login_errors' => $login_errors), 'id = \'' . $user[0]['id'] . '\'');
				if ($login_errors == 3) {
					$lock = true;
					return -1;
				}
			}
		}
		return 0;
	}
	/**
	 * Loggt einen User aus
	 *
	 * @return boolean
	 */
	public function logout()
	{
		return setcookie('ACP3_AUTH', '', time() - 50400, ROOT_DIR);
	}
	/**
	 * Setzt den internen Authentifizierungscookie
	 *
	 * @param string $nickname
	 *  Der Loginname des Users
	 * @param string $password
	 *  Die Hashsumme des Passwortes
	 * @param integer $expiry
	 *  Zeit in Sekunden, bis der Cookie seine Gültigkeit verliert
	 */
	public function setCookie($nickname, $password, $expiry)
	{
		return setcookie('ACP3_AUTH', base64_encode($nickname . '|' . $password), time() + $expiry, ROOT_DIR);
	}
}