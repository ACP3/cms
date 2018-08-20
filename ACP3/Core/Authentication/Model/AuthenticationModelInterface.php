<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Authentication\Model;

use Symfony\Component\HttpFoundation\Cookie;

interface AuthenticationModelInterface
{
    const AUTH_NAME = 'ACP3_AUTH';

    /**
     * Authenticates the user.
     *
     * @param array|int|null $userData
     */
    public function authenticate($userData);

    /**
     * Logs out the current user.
     *
     * @param int $userId
     */
    public function logout($userId = 0);

    /**
     * Setzt den internen Authentifizierungscookie.
     *
     * @param int      $userId
     * @param string   $token
     * @param int|null $expiry
     *
     * @return Cookie
     */
    public function setRememberMeCookie($userId, $token, $expiry = null);

    /**
     * Loggt einen User ein.
     *
     * @param string $username
     * @param string $password
     * @param bool   $rememberMe
     */
    public function login($username, $password, $rememberMe);
}
