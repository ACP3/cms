<?php
/**
 * Authentification
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */

if (defined('IN_ACP3') === false)
	exit;

/**
 * Authentifiziert den Benutzer
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */
class ACP3_Auth
{
	/**
	 * Name des Authentifizierungscookies
	 */
	const COOKIE_NAME = 'ACP3_AUTH';
	/**
	 * Eingeloggter Benutzer oder nicht
	 *
	 * @var boolean
	 */
	private $isUser = false;
	/**
	 * ID des Benutzers
	 *
	 * @var integer
	 */
	private $userId = 0;
	/**
	 * Super User oder nicht
	 *
	 * @var boolean
	 */
	private $superUser = false;
	/**
	 * Anzuzeigende Datensätze  pro Seite
	 *
	 * @var integer
	 */
	public $entries = CONFIG_ENTRIES;
	/**
	 * Standardsprache des Benutzers
	 *
	 * @var string
	 */
	public $language = CONFIG_LANG;
	/**
	 * Findet heraus, falls der ACP3_AUTH Cookie gesetzt ist, ob der
	 * Seitenbesucher auch wirklich ein registrierter Benutzer des ACP3 ist
	 */
	function __construct()
	{
		if (isset($_COOKIE[self::COOKIE_NAME])) {
			global $db;

			$cookie = base64_decode($_COOKIE[self::COOKIE_NAME]);
			$cookie_arr = explode('|', $cookie);

			$user = $db->select('id, super_user, pwd, entries, language', 'users', 'nickname = \'' . $db->escape($cookie_arr[0]) . '\' AND login_errors < 3');
			if (count($user) === 1) {
				$db_password = substr($user[0]['pwd'], 0, 40);
				if ($db_password === $cookie_arr[1]) {
					$this->isUser = true;
					$this->userId = (int) $user[0]['id'];
					$this->superUser = (bool) $user[0]['super_user'];
					$settings = ACP3_Config::getModuleSettings('users');
					$this->entries = $settings['entries_override'] == 1 && $user[0]['entries'] > 0 ? (int) $user[0]['entries'] : (int) CONFIG_ENTRIES;
					$this->language = $settings['language_override'] == 1 ? $user[0]['language'] : CONFIG_LANG;
				}
			} else {
				$this->logout();
			}
		}
	}
	/**
	 * Gibt die UserId des eingeloggten Benutzers zurück
	 * @return integer
	 */
	public function getUserId()
	{
		return $this->userId;
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
		if (empty($user_id) && $this->isUser() === true)
			$user_id = $this->getUserId();

		if (ACP3_Validate::isNumber($user_id) === true) {
			static $user_info = array();

			if (empty($user_info[$user_id])) {
				global $db;

				$info = $db->select('super_user, nickname, realname, gender, birthday, birthday_format, mail, website, icq, msn, skype, date_format_long, date_format_short, time_zone, language, entries, draft', 'users', 'id = \'' . $user_id . '\'');
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
					$info[0]['website'] = substr($info[0]['website'], 0, $pos);
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
	 * Gibt die eingestellte Standardsprache des Benutzers aus
	 *
	 * @return string
	 */
	public function getUserLanguage()
	{
		return $this->language;
	}
	/**
	 * Gibt den Status von $isUser zurück
	 *
	 * @return boolean
	 */
	public function isUser()
	{
		return $this->isUser === true && ACP3_Validate::isNumber($this->getUserId()) === true ? true : false;
	}
	/**
	 * Gibt aus, ob der aktuell eingeloggte Benutzer der Super User ist, oder nicht
	 *
	 * @return boolean
	 */
	public function isSuperUser()
	{
		return $this->superUser;
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

		if (count($user) === 1) {
			// Useraccount ist gesperrt
			if ($user[0]['login_errors'] >= 3)
				return -1;

			// Passwort aus Datenbank
			$db_hash = substr($user[0]['pwd'], 0, 40);

			// Hash für eingegebenes Passwort generieren
			$salt = substr($user[0]['pwd'], 41, 53);
			$form_pwd_hash = generateSaltedPassword($salt, $password);

			// Wenn beide Hashwerte gleich sind, Benutzer authentifizieren
			if ($db_hash === $form_pwd_hash) {
				// Login-Fehler zurücksetzen
				if ($user[0]['login_errors'] > 0)
					$db->update('users', array('login_errors' => 0), 'id = \'' . $user[0]['id'] . '\'');

				$this->setCookie($username, $db_hash, $expiry);

				// Neue Session-ID generieren
				ACP3_Session::secureSession(true);

				$this->isUser = true;
				$this->userId = (int) $user[0]['id'];

				return 1;
			// Beim dritten falschen Login den Account sperren
			} else {
				$login_errors = $user[0]['login_errors'] + 1;
				$db->update('users', array('login_errors' => $login_errors), 'id = \'' . $user[0]['id'] . '\'');
				if ($login_errors === 3) {
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
		global $session;
		$session->session_destroy(session_id());
		return $this->setCookie('', '', -50400);
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
		return setcookie(self::COOKIE_NAME, base64_encode($nickname . '|' . $password), time() + $expiry, '/', strpos($_SERVER['HTTP_HOST'],'.') !== false ? $_SERVER['HTTP_HOST'] : '');
	}
}