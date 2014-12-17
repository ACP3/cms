<?php

namespace ACP3\Core;

/**
 * Sessionklasse
 * Diese ist zuständig für das Sessionhandling in der Datenbank
 * @package ACP3\Core
 */
class Session
{
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
    public $expireTime = 1800;

    /**
     * Wahrscheinlichkeit, dass Session Garbage Collector anspringt
     *
     * @var integer
     */
    public $gcProbability = 10;

    /**
     * @var DB
     */
    protected $db;

    /**
     * @param DB $db
     */
    public function __construct(DB $db)
    {
        $this->db = $db;

        if (session_status() == PHP_SESSION_NONE) {
            // php.ini Session Einstellungen konfigurieren
            ini_set('session.name', self::SESSION_NAME);
            ini_set('session.use_trans_sid', 0);
            ini_set('session.use_cookies', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_httponly', 1);

            // Session GC
            ini_set('session.gc_maxlifetime', $this->expireTime);
            ini_set('session.gc_probability', $this->gcProbability);
            ini_set('session.gc_divisor', 100);

            // Eigene Session Handling Methoden setzen
            ini_set('session.save_handler', 'user');
            session_set_save_handler(
                [$this, 'session_open'],
                [$this, 'session_close'],
                [$this, 'session_read'],
                [$this, 'session_write'],
                [$this, 'session_destroy'],
                [$this, 'session_gc']
            );

            // Session starten und anschließend sichern
            self::startSession();
            self::secureSession();

            register_shutdown_function('session_write_close');
        }
    }

    /**
     * Session starten
     */
    protected static function startSession()
    {
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
    public static function secureSession($force = false)
    {
        // Session Fixation verhindern
        if (isset($_SESSION['acp3_init']) === false || $force === true) {
            session_regenerate_id(true);
            $_SESSION = [];
            $_SESSION['acp3_init'] = true;
        }
    }

    /**
     * Öffnet eine Session
     *
     * @return true
     */
    public function session_open()
    {
        return true;
    }

    /**
     * Schließt eine Session
     *
     * @return true
     */
    public function session_close()
    {
        return true;
    }

    /**
     * Liest eine Session aus der Datenbank
     *
     * @param integer $sessionId
     *
     * @return string
     */
    public function session_read($sessionId)
    {
        $session = $this->db->getConnection()->fetchAssoc('SELECT session_data FROM ' . $this->db->getPrefix() . 'sessions WHERE session_id = ?', [$sessionId]);

        // Wenn keine Session gefunden wurde, dann einen leeren String zurückgeben
        return !empty($session) ? $session['session_data'] : '';
    }

    /**
     * Session in Datenbank schreiben
     *
     * @param integer $sessionId
     * @param array   $data Enthält die Session-Daten
     *
     * @return bool
     */
    public function session_write($sessionId, $data)
    {
        $this->db->getConnection()->executeUpdate('INSERT INTO ' . $this->db->getPrefix() . 'sessions (session_id, session_starttime, session_data) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE session_data = ?', [$sessionId, time(), $data, $data]);

        return true;
    }

    /**
     * Aktuelle Session löschen
     *
     * @param integer $sessionId
     */
    public function session_destroy($sessionId)
    {
        // Alle gesetzten Session Variablen zurücksetzen
        $_SESSION = [];

        // Session-Cookie löschen
        if (isset($_COOKIE[self::SESSION_NAME])) {
            setcookie(self::SESSION_NAME, '', time() - 3600, ROOT_DIR);
        }

        // Session aus Datenbank löschen
        $this->db->getConnection()->delete($this->db->getPrefix() . 'sessions', ['session_id' => $sessionId]);
    }

    /**
     * Session Garbage Collector
     *
     * @param integer $sessionLifetime Angaben in Sekunden
     *
     * @return boolean
     */
    public function session_gc($sessionLifetime = 1800)
    {
        if ($sessionLifetime == 0) {
            return false;
        }

        $this->db->getConnection()->executeUpdate('DELETE FROM ' . $this->db->getPrefix() . 'sessions WHERE session_starttime + ? < ?', [$sessionLifetime, time()]);

        return true;
    }
}
