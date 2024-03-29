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
use ACP3\Core\Controller\Helper\ControllerActionExists;

class ACL
{
    /**
     * Array mit den dem Benutzer zugewiesenen Rollen.
     *
     * @var array<int, int[]>
     */
    private array $userRoles = [];
    /**
     * Array mit allen registrierten Ressourcen.
     *
     * @var array<string, array<string, array<string, int>>>
     */
    private array $resources = [];
    /**
     * @var PermissionEnum[]
     */
    private array $permissions = [];

    public function __construct(
        private readonly ControllerActionExists $controllerActionExists,
        private readonly UserModelInterface $user,
        private readonly UserRoleRepositoryInterface $userRoleRepository,
        private readonly PermissionServiceInterface $permissionService)
    {
    }

    /**
     * Gibt die dem jeweiligen Benutzer zugewiesenen Rollen zurück.
     *
     * @return int[]
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
     *
     * @return string[]
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
     * @return array<int, array<string, mixed>>
     */
    public function getAllRoles(): array
    {
        return $this->permissionService->getRoles();
    }

    /**
     * @return array<int, PermissionEnum>
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
        if (!$this->controllerActionExists->controllerActionExists($resource)) {
            return false;
        }

        // Fast path for the superuser, as he/she has access to all resources
        if ($this->user->isSuperUser()) {
            return true;
        }

        [$area, $module, $controller, $action] = $this->convertResourcePathToArray($resource);

        $aclResource = $module . '/' . $controller . '/' . $action . '/';

        if (isset($this->getResources()[$area][$aclResource])) {
            $resourceId = $this->getResources()[$area][$aclResource]['resource_id'];

            return $this->getPermissions()[$resourceId] !== PermissionEnum::DENY_ACCESS;
        }

        // It's okay, when a resource doesn't exist within the ACL.
        // It means, that a page doesn't need to be protected by the ACL.
        return true;
    }

    /**
     * @return string[]
     */
    private function convertResourcePathToArray(string $resource): array
    {
        $splitResource = preg_split('=/=', $resource, -1, PREG_SPLIT_NO_EMPTY);

        if ($splitResource === false) {
            $splitResource = [];
        }

        return array_replace(
            [0 => null, 1 => null, 2 => 'index', 3 => 'index'],
            $splitResource,
        );
    }

    /**
     * Gibt alle in der Datenbank vorhandenen Ressourcen zurück.
     *
     * @return array<string, array<string, array<string, int>>>
     */
    private function getResources(): array
    {
        if ($this->resources === []) {
            $this->resources = $this->permissionService->getResources();
        }

        return $this->resources;
    }
}
