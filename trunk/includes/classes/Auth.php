<?php
/**
 * Authentification
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */

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
			$cookie = base64_decode($_COOKIE[self::COOKIE_NAME]);
			$cookie_arr = explode('|', $cookie);

			$user = ACP3_CMS::$db2->executeQuery('SELECT id, super_user, pwd, entries, language FROM ' . DB_PRE . 'users WHERE nickname = ? AND login_errors < 3', array($cookie_arr[0]))->fetchAll();
			if (count($user) === 1) {
				$db_password = substr($user[0]['pwd'], 0, 40);
				if ($db_password === $cookie_arr[1]) {
					$this->isUser = true;
					$this->userId = (int) $user[0]['id'];
					$this->superUser = (bool) $user[0]['super_user'];
					$settings = ACP3_Config::getSettings('users');
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
				$info = ACP3_CMS::$db2->fetchAssoc('SELECT * FROM ' . DB_PRE . 'users WHERE id = ?', array($user_id), array(\PDO::PARAM_INT));
				if (!empty($info)) {
					$pos = strrpos($info['realname'], ':');
					$info['realname_display'] = substr($info['realname'], $pos + 1);
					$info['realname'] = substr($info['realname'], 0, $pos);
					$pos = strrpos($info['gender'], ':');
					$info['gender_display'] = substr($info['gender'], $pos + 1);
					$info['gender'] = substr($info['gender'], 0, $pos);
					$pos = strrpos($info['birthday'], ':');
					$info['birthday_display'] = substr($info['birthday'], $pos + 1);
					$info['birthday'] = substr($info['birthday'], 0, $pos);
					$pos = strrpos($info['mail'], ':');
					$info['mail_display'] = substr($info['mail'], $pos + 1);
					$info['mail'] = substr($info['mail'], 0, $pos);
					$pos = strrpos($info['website'], ':');
					$info['website_display'] = substr($info['website'], $pos + 1);
					$info['website'] = substr($info['website'], 0, $pos);
					$pos = strrpos($info['icq'], ':');
					$info['icq_display'] = substr($info['icq'], $pos + 1);
					$info['icq'] = substr($info['icq'], 0, $pos);
					$pos = strrpos($info['msn'], ':');
					$info['msn_display'] = substr($info['msn'], $pos + 1);
					$info['msn'] = substr($info['msn'], 0, $pos);
					$pos = strrpos($info['skype'], ':');
					$info['skype_display'] = substr($info['skype'], $pos + 1);
					$info['skype'] = substr($info['skype'], 0, $pos);
					$user_info[$user_id] = $info;
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
		$user = ACP3_CMS::$db2->fetchAssoc('SELECT id, pwd, login_errors FROM ' . DB_PRE . 'users WHERE nickname = ?', array($username));

		if (!empty($user)) {
			// Useraccount ist gesperrt
			if ($user['login_errors'] >= 3)
				return -1;

			// Passwort aus Datenbank
			$db_hash = substr($user['pwd'], 0, 40);

			// Hash für eingegebenes Passwort generieren
			$salt = substr($user['pwd'], 41, 53);
			$form_pwd_hash = generateSaltedPassword($salt, $password);

			// Wenn beide Hashwerte gleich sind, Benutzer authentifizieren
			if ($db_hash === $form_pwd_hash) {
				// Login-Fehler zurücksetzen
				if ($user['login_errors'] > 0)
					ACP3_CMS::$db2->update(DB_PRE . 'users', array('login_errors' => 0), array('id', (int) $user['id']));

				$this->setCookie($username, $db_hash, $expiry);

				// Neue Session-ID generieren
				ACP3_Session::secureSession(true);

				$this->isUser = true;
				$this->userId = (int) $user['id'];

				return 1;
			// Beim dritten falschen Login den Account sperren
			} else {
				$login_errors = $user['login_errors'] + 1;
				ACP3_CMS::$db2->update(DB_PRE . 'users', array('login_errors' => $login_errors), array('id' => (int) $user['id']));
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
		ACP3_CMS::$session->session_destroy(session_id());
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
		return setcookie(self::COOKIE_NAME, base64_encode($nickname . '|' . $password), time() + $expiry, ROOT_DIR, strpos($_SERVER['HTTP_HOST'],'.') !== false ? $_SERVER['HTTP_HOST'] : '');
	}
}