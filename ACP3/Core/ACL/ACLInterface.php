<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\ACL;

interface ACLInterface
{
    /**
     * @param int $roleId
     *
     * @return bool
     */
    public function userHasRole(int $roleId): bool;

    /**
     * Gibt die dem jeweiligen Benutzer zugewiesenen Rollen zurück.
     *
     * @param int $userId
     *
     * @return array
     */
    public function getUserRoleIds(int $userId): array;

    /**
     * Gibt die dem jeweiligen Benutzer zugewiesenen Rollen zurück.
     *
     * @param int $userId
     *
     * @return array
     */
    public function getUserRoleNames(int $userId): array;

    /**
     * @return array
     */
    public function getAllRoles(): array;

    /**
     * Überpüft, ob eine Modulaktion existiert und der Benutzer darauf Zugriff hat.
     *
     * @param string $resource
     *
     * @return bool
     */
    public function hasPermission(string $resource): bool;
}
