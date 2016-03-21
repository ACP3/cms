<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Session;

/**
 * Interface SessionHandlerInterface
 * @package ACP3\Core\Session
 */
interface SessionHandlerInterface extends \SessionHandlerInterface
{
    const SESSION_NAME = 'ACP3_SID';
    const XSRF_TOKEN_NAME = 'security_token';

    /**
     * @param string     $key
     *
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    public function get($key, $default = null);

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key);

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function set($key, $value);

    /**
     * @param string $key
     *
     * @return $this
     */
    public function remove($key);

    /**
     * Secures the current session
     */
    public function secureSession();
}
