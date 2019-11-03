<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core;

use ACP3\Core\ACL\Model\Repository\UserRoleRepositoryInterface;
use ACP3\Core\ACL\PermissionCacheInterface;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Controller\Helper\ControllerActionExists;

class ACL
{
    /**
     * @var \ACP3\Core\Authentication\Model\UserModelInterface
     */
    protected $user;
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Core\ACL\PermissionCacheInterface
     */
    protected $permissionsCache;
    /**
     * @var \ACP3\Core\ACL\Model\Repository\UserRoleRepositoryInterface
     */
    protected $userRoleRepository;
    /**
     * Array mit den jeweiligen Rollen zugewiesenen Berechtigungen.
     *
     * @var array
     */
    protected $privileges = [];
    /**
     * Array mit den dem Benutzer zugewiesenen Rollen.
     *
     * @var array
     */
    protected $userRoles = [];
    /**
     * Array mit allen registrierten Ressourcen.
     *
     * @var array
     */
    protected $resources = [];
    /**
     * @var \ACP3\Core\Controller\Helper\ControllerActionExists
     */
    private $controllerActionExists;

    /**
     * ACL constructor.
     *
     * @param \ACP3\Core\Modules $modules
     */
    public function __construct(
        UserModelInterface $user,
        Modules $modules,
        ControllerActionExists $controllerActionExists,
        UserRoleRepositoryInterface $userRoleRepository,
        PermissionCacheInterface $permissionsCache
    ) {
        $this->user = $user;
        $this->modules = $modules;
        $this->userRoleRepository = $userRoleRepository;
        $this->permissionsCache = $permissionsCache;
        $this->controllerActionExists = $controllerActionExists;
    }

    /**
     * Gibt die dem jeweiligen Benutzer zugewiesenen Rollen zurück.
     *
     * @return array
     */
    public function getUserRoleIds(int $userId)
    {
        if (isset($this->userRoles[$userId]) === false) {
            // Special case for guest user
            if ($userId == 0) {
                $this->userRoles[$userId][] = 1; // @TODO: Add config option for this
            } else {
                foreach ($this->userRoleRepository->getRolesByUserId($userId) as $userRole) {
                    $this->userRoles[$userId][] = $userRole['id'];
                }
            }
        }

        return $this->userRoles[$userId];
    }

    /**
     * Gibt die dem jeweiligen Benutzer zugewiesenen Rollen zurück.
     *
     * @return array
     */
    public function getUserRoleNames(int $userId)
    {
        $roles = [];
        foreach ($this->userRoleRepository->getRolesByUserId($userId) as $userRole) {
            $roles[] = $userRole['name'];
        }

        return $roles;
    }

    /**
     * @return array
     */
    public function getAllRoles()
    {
        return $this->permissionsCache->getRolesCache();
    }

    /**
     * @return bool
     */
    public function userHasRole(int $roleId)
    {
        return \in_array($roleId, $this->getUserRoleIds($this->user->getUserId()));
    }

    /**
     * Initializes the available user privileges.
     */
    protected function getPrivileges()
    {
        if ($this->privileges === []) {
            $this->privileges = $this->getRules($this->getUserRoleIds($this->user->getUserId()));
        }

        return $this->privileges;
    }

    /**
     * Returns the role permissions.
     *
     * @return array
     */
    protected function getRules(array $roleIds)
    {
        return $this->permissionsCache->getRulesCache($roleIds);
    }

    /**
     * Überpüft, ob eine Modulaktion existiert und der Benutzer darauf Zugriff hat.
     *
     * @return bool
     */
    public function hasPermission(?string $resource)
    {
        if (!empty($resource) && $this->controllerActionExists->controllerActionExists($resource) === true) {
            $resourceParts = \explode('/', $resource);

            if ($this->modules->isActive($resourceParts[1]) === true) {
                return $this->canAccessResource($resource);
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function canAccessResource(string $resource)
    {
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

    /**
     * @return array
     */
    protected function convertResourcePathToArray(string $resource)
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
     *
     * @return array
     */
    protected function getResources()
    {
        if ($this->resources === []) {
            $this->resources = $this->permissionsCache->getResourcesCache();
        }

        return $this->resources;
    }

    /**
     * Returns, whether the current user has the given privilege.
     *
     * @return bool
     */
    protected function userHasPrivilege(string $module, string $privilegeKey)
    {
        $privilegeKey = \strtolower($privilegeKey);
        if (isset($this->getPrivileges()[$module][$privilegeKey])) {
            return $this->getPrivileges()[$module][$privilegeKey]['access'];
        }

        return false;
    }
}
