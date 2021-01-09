<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core;

use ACP3\Core\ACL\Model\Repository\UserRoleRepositoryInterface;
use ACP3\Core\ACL\PermissionCacheInterface;
use ACP3\Core\Authentication\Model\UserModelInterface;

class ACL
{
    /**
     * @var \ACP3\Core\Authentication\Model\UserModelInterface
     */
    private $user;
    /**
     * @var \ACP3\Core\ACL\PermissionCacheInterface
     */
    private $permissionsCache;
    /**
     * @var \ACP3\Core\ACL\Model\Repository\UserRoleRepositoryInterface
     */
    private $userRoleRepository;
    /**
     * Array mit den den jeweiligen Rollen zugewiesenen Berechtigungen.
     *
     * @var array
     */
    private $privileges = [];
    /**
     * Array mit den dem Benutzer zugewiesenen Rollen.
     *
     * @var array
     */
    private $userRoles = [];
    /**
     * Array mit allen registrierten Ressourcen.
     *
     * @var array
     */
    private $resources = [];

    public function __construct(
        UserModelInterface $user,
        UserRoleRepositoryInterface $userRoleRepository,
        PermissionCacheInterface $permissionsCache
    ) {
        $this->user = $user;
        $this->userRoleRepository = $userRoleRepository;
        $this->permissionsCache = $permissionsCache;
    }

    /**
     * Gibt die dem jeweiligen Benutzer zugewiesenen Rollen zurück.
     *
     * @returns int[]
     */
    public function getUserRoleIds(int $userId): array
    {
        if (isset($this->userRoles[$userId]) === false) {
            // Special case for guest user
            if ($userId === 0) {
                $this->userRoles[$userId][] = 1; // @TODO: Add config option for this
            } else {
                foreach ($this->userRoleRepository->getRolesByUserId($userId) as $userRole) {
                    $this->userRoles[$userId][] = (int) $userRole['id'];
                }
            }
        }

        return $this->userRoles[$userId];
    }

    /**
     * Gibt die dem jeweiligen Benutzer zugewiesenen Rollen zurück.
     */
    public function getUserRoleNames(int $userId): array
    {
        $roles = [];
        foreach ($this->userRoleRepository->getRolesByUserId($userId) as $userRole) {
            $roles[] = $userRole['name'];
        }

        return $roles;
    }

    public function getAllRoles(): array
    {
        return $this->permissionsCache->getRolesCache();
    }

    public function userHasRole(int $roleId): bool
    {
        return \in_array($roleId, $this->getUserRoleIds($this->user->getUserId()), true);
    }

    /**
     * Initializes the available user privileges.
     */
    private function getPrivileges(): array
    {
        if ($this->privileges === []) {
            $this->privileges = $this->getRules($this->getUserRoleIds($this->user->getUserId()));
        }

        return $this->privileges;
    }

    /**
     * Returns the role permissions.
     */
    private function getRules(array $roleIds): array
    {
        return $this->permissionsCache->getRulesCache($roleIds);
    }

    /**
     * Überpüft, ob eine Modulaktion existiert und der Benutzer darauf Zugriff hat.
     */
    public function hasPermission(string $resource): bool
    {
        if (empty($resource)) {
            return false;
        }

        $resourceParts = $this->convertResourcePathToArray($resource);

        $area = $resourceParts[0];
        $resource = $resourceParts[1] . '/' . $resourceParts[2] . '/' . $resourceParts[3] . '/';

        // At least allow users to access the login page
        if (isset($this->getResources()[$area][$resource])) {
            $module = $resourceParts[1];
            $privilegeKey = $this->getResources()[$area][$resource]['key'];

            return $this->userHasPrivilege($module, $privilegeKey) === true || $this->user->isSuperUser() === true;
        }

        return false;
    }

    private function convertResourcePathToArray(string $resource): array
    {
        $resourceArray = \explode('/', $resource);

        if (empty($resourceArray[2]) === true) {
            $resourceArray[2] = 'index';
        }
        if (empty($resourceArray[3]) === true) {
            $resourceArray[3] = 'index';
        }

        return $resourceArray;
    }

    /**
     * Gibt alle in der Datenbank vorhandenen Ressourcen zurück.
     */
    private function getResources(): array
    {
        if ($this->resources === []) {
            $this->resources = $this->permissionsCache->getResourcesCache();
        }

        return $this->resources;
    }

    /**
     * Returns, whether the current user has the given privilege.
     */
    private function userHasPrivilege(string $module, string $privilegeKey): bool
    {
        $privilegeKey = \strtolower($privilegeKey);
        if (isset($this->getPrivileges()[$module][$privilegeKey])) {
            return $this->getPrivileges()[$module][$privilegeKey]['access'];
        }

        return false;
    }
}
