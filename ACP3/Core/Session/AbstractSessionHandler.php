<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Session;

abstract class AbstractSessionHandler implements SessionHandlerInterface
{
    /**
     * @var integer
     */
    protected $expireTime = 1800;
    /**
     * @var integer
     */
    protected $gcProbability = 1;
    /**
     * @var int
     */
    protected $gcDivisor = 100;

    /**
     * Configures the session
     * @param bool $isSecure
     */
    protected function configureSession($isSecure = false)
    {
        if (session_status() == PHP_SESSION_NONE) {
            // Configure the php.ini session settings
            ini_set('session.name', self::SESSION_NAME);
            ini_set('session.use_trans_sid', 0);
            ini_set('session.use_cookies', 'on');
            ini_set('session.use_only_cookies', 'on');
            ini_set('session.cookie_httponly', 'on');
            ini_set('session.use_strict_mode', 'on');
            ini_set('session.cookie_secure', $isSecure ? 'on' : 'off');

            // Session GC
            ini_set('session.gc_maxlifetime', $this->expireTime);
            ini_set('session.gc_probability', $this->gcProbability);
            ini_set('session.gc_divisor', $this->gcDivisor);

            // Set our own session handling methods
            ini_set('session.save_handler', 'user');
            session_set_save_handler($this, true);

            session_register_shutdown();

            // Start the session and secure it
            $this->startSession();
        }
    }

    /**
     * Starts the session
     */
    abstract protected function startSession();

    /**
     * @param string     $key
     *
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        return $this->has($key) ? $_SESSION[$key] : $default;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $_SESSION);
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function remove($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }

        return $this;
    }
}
