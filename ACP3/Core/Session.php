<?php

namespace ACP3\Core;

/**
 * @package ACP3\Core
 */
class Session
{
    const SESSION_NAME = 'ACP3_SID';
    const XSRF_TOKEN_NAME = 'security_token';
    /**
     * @var integer
     */
    public $expireTime = 1800;
    /**
     * @var integer
     */
    public $gcProbability = 10;
    /**
     * @var \ACP3\Core\DB
     */
    protected $db;

    /**
     * @param \ACP3\Core\DB $db
     */
    public function __construct(DB $db)
    {
        $this->db = $db;

        if (session_status() == PHP_SESSION_NONE) {
            // Configure the php.ini session settings
            ini_set('session.name', self::SESSION_NAME);
            ini_set('session.use_trans_sid', 0);
            ini_set('session.use_cookies', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_httponly', 1);

            // Session GC
            ini_set('session.gc_maxlifetime', $this->expireTime);
            ini_set('session.gc_probability', $this->gcProbability);
            ini_set('session.gc_divisor', 100);

            // Set our own session handling methods
            ini_set('session.save_handler', 'user');
            session_set_save_handler(
                [$this, 'session_open'],
                [$this, 'session_close'],
                [$this, 'session_read'],
                [$this, 'session_write'],
                [$this, 'session_destroy'],
                [$this, 'session_gc']
            );

            // Start the session and secure it
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
        // Set the session cookie parameters
        session_set_cookie_params(0, ROOT_DIR);

        // Start the session
        session_start();
    }

    /**
     * Secures the current session
     *
     * @param boolean $force
     */
    public static function secureSession($force = false)
    {
        // Prevend from session fixations
        if (isset($_SESSION['acp3_init']) === false || $force === true) {
            session_regenerate_id(true);
            $_SESSION = [];
            $_SESSION['acp3_init'] = true;
        }
    }

    /**
     * Opens a session
     *
     * @return true
     */
    public function session_open()
    {
        return true;
    }

    /**
     * Closes the current session
     *
     * @return true
     */
    public function session_close()
    {
        return true;
    }

    /**
     * @param $key
     *
     * @return mixed|null
     */
    public function getParameter($key)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    /**
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function setParameter($key, $value)
    {
        $_SESSION[$key] = $value;

        return $this;
    }

    /**
     * Reads a session from the database
     *
     * @param integer $sessionId
     *
     * @return string
     */
    public function session_read($sessionId)
    {
        $session = $this->db->fetchColumn('SELECT `session_data` FROM ' . $this->db->getPrefix() . 'sessions WHERE `session_id` = ?', [$sessionId]);

        return $session ?: ''; // Return an empty string, if the requested session can't be found
    }

    /**
     * Writes a session to the database
     *
     * @param integer $sessionId
     * @param array   $data Contains the session data
     *
     * @return bool
     */
    public function session_write($sessionId, $data)
    {
        $this->db->getConnection()->executeUpdate('INSERT INTO ' . $this->db->getPrefix() . 'sessions (session_id, session_starttime, session_data) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE `session_data` = ?', [$sessionId, time(), $data, $data]);

        return true;
    }

    /**
     * Deletes the current session
     *
     * @param integer $sessionId
     */
    public function session_destroy($sessionId)
    {
        // Reset all already set session variables
        $_SESSION = [];

        // Session-Cookie löschen
        if (isset($_COOKIE[self::SESSION_NAME])) {
            setcookie(self::SESSION_NAME, '', time() - 3600, ROOT_DIR);
        }

        // Delete the session from the database
        $this->db->getConnection()->delete($this->db->getPrefix() . 'sessions', ['session_id' => $sessionId]);
    }

    /**
     * Session Garbage Collector
     *
     * @param integer $sessionLifetime Time in seconds
     *
     * @return boolean
     */
    public function session_gc($sessionLifetime = 1800)
    {
        if ($sessionLifetime == 0) {
            return false;
        }

        $this->db->getConnection()->executeUpdate('DELETE FROM ' . $this->db->getPrefix() . 'sessions WHERE `session_starttime` + ? < ?', [$sessionLifetime, time()]);

        return true;
    }
}
