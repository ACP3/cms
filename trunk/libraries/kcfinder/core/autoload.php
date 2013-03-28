<?php

/** This file is part of KCFinder project
  *
  *      @desc This file is included first, before each other
  *   @package KCFinder
  *   @version 2.52-dev
  *    @author Pavel Tzonkov <pavelc@users.sourceforge.net>
  * @copyright 2010, 2011 KCFinder Project
  *   @license http://www.opensource.org/licenses/gpl-2.0.php GPLv2
  *   @license http://www.opensource.org/licenses/lgpl-2.1.php LGPLv2
  *      @link http://kcfinder.sunhater.com
  *
  * This file is the place you can put any code (at the end of the file),
  * which will be executed before any other. Suitable for:
  *     1. Set PHP ini settings using ini_set()
  *     2. Custom session save handler with session_set_save_handler()
  *     3. Any custom integration code. If you use any global variables
  *        here, they can be accessed in config.php via $GLOBALS array.
  *        It's recommended to use constants instead.
  */


// PHP VERSION CHECK
if (substr(PHP_VERSION, 0, strpos(PHP_VERSION, '.')) < 5)
    die("You are using PHP " . PHP_VERSION . " when KCFinder require at least version 5! Some systems has an option to change the active PHP version. Please refer to your hosting provider or upgrade your PHP distribution.");


// SAFE MODE CHECK
if (ini_get("safe_mode"))
    die("The \"safe_mode\" PHP ini setting is turned on! You cannot run KCFinder in safe mode.");


// CMS INTEGRATION
if (isset($_GET['cms'])) {
    switch ($_GET['cms']) {
        case "acp3":
			require "integration/acp3.php";
			break;
        case "drupal":
			require "integration/drupal.php";
    }
}

/**
 * Autoloading für die ACP3 eigenen Klassen
 *
 * @param string $class
 *  Der Name der zu ladenden Klasse
 */
function kcfinder_autoload($class)
{
    if ($class == "uploader")
		require "core/uploader.php";
	elseif ($class == "browser")
		require "core/browser.php";
	elseif (file_exists("core/types/$class.php"))
		require "core/types/$class.php";
	elseif (file_exists("lib/class_$class.php"))
		require "lib/class_$class.php";
	elseif (file_exists("lib/helper_$class.php"))
		require "lib/helper_$class.php";
}

spl_autoload_register('kcfinder_autoload');

// json_encode() IMPLEMENTATION IF JSON EXTENSION IS MISSING
if (!function_exists("json_encode")) {

    function kcfinder_json_string_encode($string) {
        return '"' .
            str_replace('/', "\\/",
            str_replace("\t", "\\t",
            str_replace("\r", "\\r",
            str_replace("\n", "\\n",
            str_replace('"', "\\\"",
            str_replace("\\", "\\\\",
        $string)))))) . '"';
    }

    function json_encode($data) {

        if (is_array($data)) {
            $ret = array();

            // OBJECT
            if (array_keys($data) !== range(0, count($data) - 1)) {
                foreach ($data as $key => $val)
                    $ret[] = kcfinder_json_string_encode($key) . ':' . json_encode($val);
                return "{" . implode(",", $ret) . "}";

            // ARRAY
            } else {
                foreach ($data as $val)
                    $ret[] = json_encode($val);
                return "[" . implode(",", $ret) . "]";
            }

        // BOOLEAN OR NULL
        } elseif (is_bool($data) || ($data === null))
            return ($data === null)
                ? "null"
                : ($data ? "true" : "false");

        // FLOAT
        elseif (is_float($data))
            return rtrim(rtrim(number_format($data, 14, ".", ""), "0"), ".");

        // INTEGER
        elseif (is_int($data))
            return $data;

        // STRING
        return kcfinder_json_string_encode($data);
    }
}


class ACP3_Session {
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
		session_set_save_handler(
				array($this, 'session_open'),
				array($this, 'session_close'),
				array($this, 'session_read'),
				array($this, 'session_write'),
				array($this, 'session_destroy'),
				array($this, 'session_gc')
		);

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
		session_set_cookie_params(0, ROOT_DIR);

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
		$session = ACP3_CMS::$db2->fetchAssoc('SELECT session_data FROM ' . DB_PRE . 'sessions WHERE session_id = ?', array($session_id));

		// Wenn keine Session gefunden wurde, dann einen leeren String zurückgeben
		return !empty($session) ? $session['session_data'] : '';
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
		ACP3_CMS::$db2->executeUpdate('INSERT INTO ' . DB_PRE . 'sessions (session_id, session_starttime, session_data) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE session_data = ?', array($session_id, time(), $data, $data));

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
			setcookie(self::SESSION_NAME, '', time() - 3600, ROOT_DIR);

		// Session aus Datenbank löschen
		ACP3_CMS::$db2->delete(DB_PRE . 'sessions', array('session_id' => $session_id));
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

		ACP3_CMS::$db2->executeUpdate('DELETE FROM ' . DB_PRE . 'sessions WHERE session_starttime + ? < ?', array($session_lifetime, time()));

		return true;
	}
}
?>