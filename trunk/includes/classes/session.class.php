<?php
/**
 * Sessions
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
if (defined('IN_ACP3') === false)
	exit;

/**
 * Sessionklasse
 * Diese ist zuständig für das Sessionhandling in der Datenbank
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
class session {
	/**
	 * Name der Session
	 */
	const SESSION_NAME = 'ACP3_SID';
	/**
	 * Name des XSRF-Token
	 */
	const XSRF_TOKEN_NAME = 'security_token';
	/**
	 * Zeit, bis Session ungültig wird
	 *
	 * @var integer
	 */
	public $expire_time = 1800;
	/**
	 * Wahrscheinlichkeit, dass Session Garbage Collector anspringt
	 *
	 * @var integer
	 */
	public $gc_probability = 10;

	public function __construct() {
		// php.ini Session Einstellungen konfigurieren
		ini_set('session.name', self::SESSION_NAME);
		ini_set('session.use_trans_sid', 0);
		ini_set('session.use_cookies', 1);
		ini_set('session.use_only_cookies', 1);
		ini_set('session.cookie_httponly', 1);

		// Session GC
		ini_set('session.gc_maxlifetime', $this->expire_time);
		ini_set('session.gc_probability', $this->gc_probability);
		ini_set('session.gc_divisor', 100);

		// Eigene Session Handling Methoden setzen
		ini_set('session.save_handler', 'user');
		session_set_save_handler(array($this, 'session_open'), array($this, 'session_close'), array($this, 'session_read'), array($this, 'session_write'), array($this, 'session_destroy'), array($this, 'session_gc'));

		// Session starten und anschließend sichern
		self::startSession();
		self::secureSession();

		register_shutdown_function('session_write_close');
	}
	/**
	 * Session starten
	 */
	private static function startSession() {
		// Session Cookie Parameter setzen
		session_set_cookie_params(0, '/');

		// Session starten
		session_start();
	}
	/**
	 * Sichert die aktuelle Session
	 *
	 * @param boolean $force
	 */
	public static function secureSession($force = false) {
		// Session Fixation verhindern
		if (isset($_SESSION['acp3_init']) === false || $force === true) {
			session_regenerate_id(true);
			$_SESSION = array();
			$_SESSION['acp3_init'] = true;
		}
	}
	/**
	 * Öffnet eine Session
	 *
	 * @return true
	 */
	public function session_open() {
		return true;
	}

	/**
	 * Schließt eine Session
	 *
	 * @return true
	 */
	public function session_close() {
		return true;
	}

	/**
	 * Liest eine Session aus der Datenbank
	 *
	 * @param integer $session_id
	 * @return string
	 */
	public function session_read($session_id) {
		global $db;

		$session = $db->select('session_data', 'sessions', 'session_id = \'' . $db->escape($session_id) . '\'');

		// Wenn keine Session gefunden wurde, dann einen leeren String zurückgeben
		return !empty($session) ? (string) $session[0]['session_data'] : '';
	}

	/**
	 * Session in Datenbank schreiben
	 *
	 * @param integer $session_id
	 * @param array $data Enthält die Session-Daten
	 *
	 * @return bool
	 */
	public function session_write($session_id, $data) {
		global $db;

		$db->query('INSERT INTO {pre}sessions (session_id, session_starttime, session_data) VALUES (\'' . $db->escape($session_id) . '\', \'' . time() . '\', \'' . $data . '\') ON DUPLICATE KEY UPDATE session_data = \'' . $data . '\'', 0);

		return true;
	}

	/**
	 * Aktuelle Session löschen
	 *
	 * @param integer $session_id
	 */
	public function session_destroy($session_id) {
		// Alle gesetzten Session Variablen zurücksetzen
		$_SESSION = array();

		// Session-Cookie löschen
		if (isset($_COOKIE[self::SESSION_NAME]))
			setcookie(self::SESSION_NAME, '', time() - 3600, '/');

		// Session aus Datenbank löschen
		global $db;
		$db->delete('sessions', 'session_id = \'' . $db->escape($session_id) . '\'');
	}

	/**
	 * Session Garbage Collector
	 *
	 * @param integer $session_lifetime Angaben in Sekunden
	 *
	 * @return boolean
	 */
	public function session_gc($session_lifetime = 1800) {
		if ($session_lifetime == 0)
			return;

		global $db;
		$db->delete('sessions', 'session_starttime < ' . (time() + $session_lifetime));

		return true;
	}
	/**
	 * Generiert für ein Formular ein Securitytoken
	 */
	public function generateFormToken()
	{
		global $tpl;

		if (!isset($_SESSION[self::XSRF_TOKEN_NAME]) || is_array($_SESSION[self::XSRF_TOKEN_NAME]) === false) {
			$_SESSION[self::XSRF_TOKEN_NAME] = array();
		}

		$token = md5(uniqid(mt_rand(), true));
		$_SESSION[self::XSRF_TOKEN_NAME][] = $token;

		$tpl->assign('form_token', '<input type="hidden" name="' . self::XSRF_TOKEN_NAME . '" value="' . $token . '" />');
	}
	/**
	 * Entfernt das Securitytoken aus der Session
	 */
	public function unsetFormToken($token = '')
	{
		if (empty($token) && isset($_POST[self::XSRF_TOKEN_NAME]))
			$token = $_POST[self::XSRF_TOKEN_NAME];
		if (!empty($token) && is_array($_SESSION[self::XSRF_TOKEN_NAME])) {
			foreach ($_SESSION[self::XSRF_TOKEN_NAME] as $key => $value) {
				if ($value === $token) {
					unset($_SESSION[self::XSRF_TOKEN_NAME][$key]);
					break;
				}
			}
		}
	}
}