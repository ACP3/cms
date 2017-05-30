<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\ACL;

interface ACLInterface
{
    /**
     * @param integer $roleId
     *
     * @return boolean
     */
    public function userHasRole(int $roleId): bool;

    /**
     * Gibt die dem jeweiligen Benutzer zugewiesenen Rollen zurück
     *
     * @param integer $userId
     *
     * @return array
     */
    public function getUserRoleIds(int $userId): array;

    /**
     * Gibt die dem jeweiligen Benutzer zugewiesenen Rollen zurück
     *
     * @param integer $userId
     *
     * @return array
     */
    public function getUserRoleNames(int $userId): array;

    /**
     * @return array
     */
    public function getAllRoles(): array;

    /**
     * Überpüft, ob eine Modulaktion existiert und der Benutzer darauf Zugriff hat
     *
     * @param string $resource
     *
     * @return boolean
     */
    public function hasPermission(string $resource): bool;
}
