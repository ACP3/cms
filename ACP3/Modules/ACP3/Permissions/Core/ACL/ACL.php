<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Core\ACL;

use ACP3\Core\ACL\ACLInterface;
use ACP3\Core\ACL\Model\Repository\AclUserRolesRepositoryInterface;
use ACP3\Core\Modules;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\Users\Model\UserModel;

class ACL implements ACLInterface
{
    /**
     * Array mit den den jeweiligen Rollen zugewiesenen Berechtigungen
     *
     * @var array
     */
    private $privileges = [];
    /**
     * Array mit den dem Benutzer zugewiesenen Rollen
     *
     * @var array
     */
    private $userRoles = [];
    /**
     * Array mit allen registrierten Ressourcen
     *
     * @var array
     */
    private $resources = [];

    /**
     * @var \ACP3\Modules\ACP3\Users\Model\UserModel
     */
    private $user;
    /**
     * @var \ACP3\Core\Modules\Modules
     */
    private $modules;
    /**
     * @var \ACP3\Modules\ACP3\Permissions\Cache\PermissionsCacheStorage
     */
    private $permissionsCache;
    /**
     * @var \ACP3\Core\ACL\Model\Repository\AclUserRolesRepositoryInterface
     */
    private $userRoleRepository;
    /**
     * @var Modules\Helper\ControllerActionExists
     */
    private $controllerActionExists;

    /**
     * ACL constructor.
     * @param \ACP3\Modules\ACP3\Users\Model\UserModel $user
     * @param \ACP3\Core\Modules\Modules $modules
     * @param Modules\Helper\ControllerActionExists $controllerActionExists
     * @param \ACP3\Core\ACL\Model\Repository\AclUserRolesRepositoryInterface $userRoleRepository
     * @param \ACP3\Modules\ACP3\Permissions\Cache\PermissionsCacheStorage $permissionsCache
     */
    public function __construct(
        UserModel $user,
        Modules\Modules $modules,
        Modules\Helper\ControllerActionExists $controllerActionExists,
        AclUserRolesRepositoryInterface $userRoleRepository,
        Permissions\Cache\PermissionsCacheStorage $permissionsCache
    ) {
        $this->user = $user;
        $this->modules = $modules;
        $this->userRoleRepository = $userRoleRepository;
        $this->permissionsCache = $permissionsCache;
        $this->controllerActionExists = $controllerActionExists;
    }

    /**
     * @inheritdoc
     */
    public function userHasRole(int $roleId): bool
    {
        return \in_array($roleId, $this->getUserRoleIds($this->user->getUserId()));
    }

    /**
     * @inheritdoc
     */
    public function getUserRoleIds(int $userId): array
    {
        if (isset($this->userRoles[$userId]) === false) {
            // Special case for guest users
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
     * @inheritdoc
     */
    public function getUserRoleNames(int $userId): array
    {
        $roles = [];
        foreach ($this->userRoleRepository->getRolesByUserId($userId) as $userRole) {
            $roles[] = $userRole['name'];
        }

        return $roles;
    }

    /**
     * @inheritdoc
     */
    public function getAllRoles(): array
    {
        return $this->permissionsCache->getRolesCache();
    }

    /**
     * Returns the role permissions
     *
     * @param array $roleIds
     *
     * @return array
     */
    private function getRules(array $roleIds): array
    {
        return $this->permissionsCache->getRulesCache($roleIds);
    }

    /**
     * @inheritdoc
     */
    public function hasPermission(string $resource): bool
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
     * @param string $resource
     *
     * @return boolean
     */
    private function canAccessResource(string $resource): bool
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
     * @param string $resource
     *
     * @return array
     */
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
     * Gibt alle in der Datenbank vorhandenen Ressourcen zurück
     *
     * @return array
     */
    private function getResources(): array
    {
        if ($this->resources === []) {
            $this->resources = $this->permissionsCache->getResourcesCache();
        }

        return $this->resources;
    }

    /**
     * Returns, whether the current user has the given privilege
     *
     * @param string $module
     * @param string $privilegeKey
     *
     * @return boolean
     */
    private function userHasPrivilege(string $module, string $privilegeKey): bool
    {
        $privilegeKey = \strtolower($privilegeKey);

        return $this->getPrivileges()[$module][$privilegeKey]['access'] ?? false;
    }

    /**
     * Initializes the available user privileges
     *
     * @return array
     */
    private function getPrivileges(): array
    {
        if ($this->privileges === []) {
            $this->privileges = $this->getRules($this->getUserRoleIds($this->user->getUserId()));
        }

        return $this->privileges;
    }
}
