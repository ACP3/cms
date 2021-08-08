<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core;

use ACP3\Core\ACL\PermissionEnum;
use ACP3\Core\ACL\PermissionServiceInterface;
use ACP3\Core\ACL\Repository\UserRoleRepositoryInterface;
use ACP3\Core\Authentication\Model\UserModelInterface;

class ACL
{
    /**
     * @var \ACP3\Core\Authentication\Model\UserModelInterface
     */
    private $user;
    /**
     * @var PermissionServiceInterface
     */
    private $permissionService;
    /**
     * @var \ACP3\Core\ACL\Repository\UserRoleRepositoryInterface
     */
    private $userRoleRepository;

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
    /**
     * @var int[]
     */
    private $permissions = [];

    public function __construct(
        UserModelInterface $user,
        UserRoleRepositoryInterface $userRoleRepository,
        PermissionServiceInterface $permissionService
    ) {
        $this->user = $user;
        $this->userRoleRepository = $userRoleRepository;
        $this->permissionService = $permissionService;
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
        return $this->permissionService->getRoles();
    }

    public function userHasRole(int $roleId): bool
    {
        return \in_array($roleId, $this->getUserRoleIds($this->user->getUserId()), true);
    }

    /**
     * @return int[]
     */
    private function getPermissions(): array
    {
        if ($this->permissions === []) {
            $this->permissions = $this->permissionService->getPermissionsWithInheritance($this->getUserRoleIds($this->user->getUserId()));
        }

        return $this->permissions;
    }

    /**
     * Überprüft, ob eine Modulaktion existiert und der Benutzer darauf Zugriff hat.
     */
    public function hasPermission(string $resource): bool
    {
        [$area, $module, $controller, $action] = $this->convertResourcePathToArray($resource);

        if (empty($module)) {
            return false;
        }

        $resource = $module . '/' . $controller . '/' . $action . '/';

        if (isset($this->getResources()[$area][$resource])) {
            $resourceId = $this->getResources()[$area][$resource]['resource_id'];

            return $this->getPermissions()[$resourceId] !== PermissionEnum::DENY_ACCESS || $this->user->isSuperUser() === true;
        }

        return false;
    }

    private function convertResourcePathToArray(string $resource): array
    {
        return array_replace(
            [0 => null, 1 => null, 2 => 'index', 3 => 'index'],
            preg_split('=/=', $resource, -1, PREG_SPLIT_NO_EMPTY)
        );
    }

    /**
     * Gibt alle in der Datenbank vorhandenen Ressourcen zurück.
     */
    private function getResources(): array
    {
        if ($this->resources === []) {
            $this->resources = $this->permissionService->getResources();
        }

        return $this->resources;
    }
}
