<?php

namespace ACP3\Core;

/**
 * @package ACP3\Core
 */
class SessionHandler implements \SessionHandlerInterface
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
     * @var bool
     */
    private $gcCalled = false;
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

        $this->configureSession();
    }

    protected function configureSession()
    {
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
            session_set_save_handler($this, true);

            // Start the session and secure it
            $this->startSession();
            $this->secureSession();

            register_shutdown_function('session_write_close');
        }
    }

    /**
     * Session starten
     */
    protected function startSession()
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
    public function secureSession($force = false)
    {
        // Prevent from session fixations
        if ($this->getParameter('acp3_init', false) === false || $force === true) {
            session_regenerate_id(true);
            $this->resetSessionData();
            $this->setParameter('acp3_init', true);
        }
    }

    /**
     * @inheritdoc
     */
    public function open($savePath, $sessionId)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function close()
    {
        if ($this->gcCalled === true) {
            if ($this->expireTime === 0) {
                return false;
            }

            $this->gcCalled = false;
            $this->db->getConnection()->executeUpdate("DELETE FROM `{$this->db->getPrefix()}sessions` WHERE `session_starttime` + ? < ?", [$this->expireTime, time()]);
        }

        return true;
    }

    /**
     * @param string     $key
     *
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    public function getParameter($key, $default = null)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
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

    public function unsetParameter($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }

        return $this;
    }

    /**
     * Resets all already stored session data
     */
    protected function resetSessionData()
    {
        $_SESSION = [];
    }

    /**
     * @inheritdoc
     */
    public function read($sessionId)
    {
        $session = $this->db->fetchColumn("SELECT `session_data` FROM `{$this->db->getPrefix()}sessions` WHERE `session_id` = ?", [$sessionId]);

        return $session ?: ''; // Return an empty string, if the requested session can't be found
    }

    /**
     * @inheritdoc
     */
    public function write($sessionId, $data)
    {
        $this->db->getConnection()->executeUpdate("INSERT INTO `{$this->db->getPrefix()}sessions` (`session_id`, `session_starttime`, `session_data`) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE `session_data` = ?", [$sessionId, time(), $data, $data]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function destroy($sessionId)
    {
        $this->resetSessionData();

        // Session-Cookie löschen
        if (isset($_COOKIE[self::SESSION_NAME])) {
            setcookie(self::SESSION_NAME, '', time() - 3600, ROOT_DIR);
        }

        // Delete the session from the database
        $this->db->getConnection()->delete($this->db->getPrefix() . 'sessions', ['session_id' => $sessionId]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function gc($sessionLifetime)
    {
        // Delay the garbage collection to the close() method, to prevent from read/write locks
        $this->gcCalled = true;

        return true;
    }

}