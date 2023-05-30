<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Authentication\Model;

use Symfony\Component\HttpFoundation\Cookie;

interface AuthenticationModelInterface
{
    public const AUTH_NAME = 'ACP3_AUTH';

    /**
     * Authenticates the user.
     *
     * @param array<string, mixed>|null $userData
     */
    public function authenticate(?array $userData): void;

    /**
     * Logs out the current user.
     */
    public function logout(int $userId = 0): void;

    /**
     * Setzt den internen Authentifizierungscookie.
     */
    public function setRememberMeCookie(int $userId, string $token, int $expiry = null): Cookie;

    /**
     * Loggt einen User ein.
     */
    public function login(string $username, string $password, bool $rememberMe): void;
}
