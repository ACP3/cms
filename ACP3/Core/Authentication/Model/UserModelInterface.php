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
    public function getUserInfo($userId = 0);

    /**
     * Returns, whether the current user is an authenticated user or not.
     *
     * @return bool
     */
    public function isAuthenticated();

    /**
     * @param bool $isAuthenticated
     *
     * @return $this
     */
    public function setIsAuthenticated($isAuthenticated);

    /**
     * Returns the user id of the currently logged in user.
     *
     * @return int
     */
    public function getUserId();

    /**
     * @param int $userId
     *
     * @return $this
     */
    public function setUserId($userId);

    /**
     * Returns, whether the currently logged in user is a super user or not.
     *
     * @return bool
     */
    public function isSuperUser();

    /**
     * @param bool $isSuperUser
     *
     * @return $this
     */
    public function setIsSuperUser($isSuperUser);
}
