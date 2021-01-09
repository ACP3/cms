<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\ACL;

interface PermissionCacheInterface
{
    /**
     * Returns the cache of all the registered resources.
     * This will also only return the resources of the currently installed and active modules.
     *
     * @return array
     */
    public function getResourcesCache();

    /**
     * Erstellt den Cache für alle existierenden Ressourcen.
     *
     * @return bool
     */
    public function saveResourcesCache();

    /**
     * @return array
     */
    public function getRolesCache();

    /**
     * Setzt den Cache für alle existierenden Rollen.
     *
     * @return bool
     */
    public function saveRolesCache();

    /**
     * @return array
     */
    public function getRulesCache(array $roles);

    /**
     * Setzt den Cache für die einzelnen Berechtigungen einer Rolle.
     *
     * @return bool
     */
    public function saveRulesCache(array $roles);
}
