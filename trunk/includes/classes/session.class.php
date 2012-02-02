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
	const session_name = 'ACP3_SID';

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

	function __construct() {
		// php.ini Session Einstellungen konfigurieren
		ini_set('session.name', self::session_name);
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
		self::startSession($this->expire_time);
		self::secureSession();

		register_shutdown_function('session_write_close');
	}
	/**
	 * Session starten
	 *
	 * @param integer $expire_time Angaben in Sekunden
	 */
	private static function startSession($expire_time = 1800) {
		// Session Cookie Parameter setzen
		session_set_cookie_params($expire_time);

		// Session starten und bei Erfolg Session-Cookie setzen
		if (session_start() === true) {
			setcookie(self::session_name, session_id(), time() + $expire_time, ROOT_DIR);
		}
	}
	/**
	 * Sichert die aktuelle Session
	 */
	private static function secureSession() {
		// Session Fixation verhindern
		if (isset($_SESSION['acp3_init']) === false) {
			session_regenerate_id(true);
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
		if (isset($_COOKIE[self::session_name]))
			setcookie(self::session_name, '', time() - 3600, ROOT_DIR);

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
	 * Setzt eine Session Variable
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function set($key, $value) {
		$_SESSION[$key] = $value;
	}
	/**
	 * Liest Daten aus der Session aus
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function get($key) {
		return isset($_SESSION[$key]) ? $_SESSION[$key] : false;
	}
}