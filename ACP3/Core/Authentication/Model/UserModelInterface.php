<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Authentication\Model;

interface UserModelInterface
{
    /**
     * Gibt ein Array mit den angeforderten Daten eines Benutzers zurück.
     *
     * @param int $userId
     *
     * @return array
     */
    public function getUserInfo(int $userId = 0): array;

    /**
     * Returns, whether the current user is an authenticated user or not.
     *
     * @return bool
     */
    public function isAuthenticated(): bool;

    /**
     * @param bool $isAuthenticated
     *
     * @return $this
     */
    public function setIsAuthenticated(bool $isAuthenticated);

    /**
     * Returns the user id of the currently logged in user.
     *
     * @return int
     */
    public function getUserId(): int;

    /**
     * @param int $userId
     *
     * @return $this
     */
    public function setUserId(int $userId);

    /**
     * Returns, whether the currently logged in user is a super user or not.
     *
     * @return bool
     */
    public function isSuperUser(): bool;

    /**
     * @param bool $isSuperUser
     *
     * @return $this
     */
    public function setIsSuperUser(bool $isSuperUser);
}
