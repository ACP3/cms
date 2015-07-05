<?php
namespace ACP3\Installer\Core;

/**
 * Class SessionHandler
 * @package ACP3\Installer\Core
 */
class SessionHandler extends \ACP3\Core\SessionHandler
{
    public function __construct()
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
        }
    }

    /**
     * Session starten
     */
    protected function startSession()
    {

    }

    /**
     * Secures the current session
     *
     * @param boolean $force
     */
    public function secureSession($force = false)
    {

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
        return true;
    }

    /**
     * @inheritdoc
     */
    public function read($sessionId)
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function write($sessionId, $data)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function destroy($sessionId)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function gc($sessionLifetime)
    {
        return true;
    }

}