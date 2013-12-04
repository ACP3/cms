<?php

namespace ACP3\Core;

/**
 * Sessionklasse
 * Diese ist zuständig für das Sessionhandling in der Datenbank
 *
 * @author Tino Goratsch
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
    public $expire_time = 1800;

    /**
     * Wahrscheinlichkeit, dass Session Garbage Collector anspringt
     *
     * @var integer
     */
    public $gc_probability = 10;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $db;

    /**
     * @var \ACP3\Core\URI
     */
    private $uri;

    /**
     * @var \ACP3\Core\View
     */
    private $view;

    public function __construct(\Doctrine\DBAL\Connection $db, \ACP3\Core\URI $uri, \ACP3\Core\View $view)
    {
        $this->db = $db;
        $this->uri = $uri;
        $this->view = $view;

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
            array($this, 'session_open'), array($this, 'session_close'), array($this, 'session_read'), array($this, 'session_write'), array($this, 'session_destroy'), array($this, 'session_gc')
        );

        // Session starten und anschließend sichern
        self::startSession();
        self::secureSession();

        register_shutdown_function('session_write_close');
    }

    /**
     * Session starten
     */
    private static function startSession()
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
            $_SESSION = array();
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
     * @param integer $session_id
     * @return string
     */
    public function session_read($session_id)
    {
        $session = $this->db->fetchAssoc('SELECT session_data FROM ' . DB_PRE . 'sessions WHERE session_id = ?', array($session_id));

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
    public function session_write($session_id, $data)
    {
        $this->db->executeUpdate('INSERT INTO ' . DB_PRE . 'sessions (session_id, session_starttime, session_data) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE session_data = ?', array($session_id, time(), $data, $data));

        return true;
    }

    /**
     * Aktuelle Session löschen
     *
     * @param integer $session_id
     */
    public function session_destroy($session_id)
    {
        // Alle gesetzten Session Variablen zurücksetzen
        $_SESSION = array();

        // Session-Cookie löschen
        if (isset($_COOKIE[self::SESSION_NAME])) {
            setcookie(self::SESSION_NAME, '', time() - 3600, ROOT_DIR);
        }

        // Session aus Datenbank löschen
        Registry::get('Db')->delete(DB_PRE . 'sessions', array('session_id' => $session_id));
    }

    /**
     * Session Garbage Collector
     *
     * @param integer $session_lifetime Angaben in Sekunden
     *
     * @return boolean
     */
    public function session_gc($session_lifetime = 1800)
    {
        if ($session_lifetime == 0) {
            return;
        }

        $this->db->executeUpdate('DELETE FROM ' . DB_PRE . 'sessions WHERE session_starttime + ? < ?', array($session_lifetime, time()));

        return true;
    }

    /**
     * Generiert für ein Formular ein Securitytoken
     *
     * @param string $path
     *    Optionaler ACP3 interner URI Pfad, für welchen das Token gelten soll
     */
    public function generateFormToken($path = '')
    {
        if (!isset($_SESSION[self::XSRF_TOKEN_NAME]) || is_array($_SESSION[self::XSRF_TOKEN_NAME]) === false) {
            $_SESSION[self::XSRF_TOKEN_NAME] = array();
        }

        $token = sha1(uniqid(mt_rand(), true));
        $path = !empty($path) ? $path . (!preg_match('/\/$/', $path) ? '/' : '') : $this->uri->query;
        $_SESSION[self::XSRF_TOKEN_NAME][$path] = $token;

        $this->view->assign('form_token', '<input type="hidden" name="' . self::XSRF_TOKEN_NAME . '" value="' . $token . '" />');
    }

    /**
     * Entfernt das Securitytoken aus der Session
     */
    public function unsetFormToken($token = '')
    {
        if (empty($token) && isset($_POST[self::XSRF_TOKEN_NAME])) {
            $token = $_POST[self::XSRF_TOKEN_NAME];
        }
        if (!empty($token) && is_array($_SESSION[self::XSRF_TOKEN_NAME]) === true) {
            if (isset($_SESSION[self::XSRF_TOKEN_NAME][$this->uri->query])) {
                unset($_SESSION[self::XSRF_TOKEN_NAME][$this->uri->query]);
            }
        }
    }

}
