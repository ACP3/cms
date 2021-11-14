<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Services;

use ACP3\Core\ACL\PermissionServiceInterface;
use Psr\Cache\CacheItemPoolInterface;

class CachingPermissionService implements PermissionServiceInterface
{
    private const CACHE_ID_RESOURCES = 'acl_resources';
    private const CACHE_ID_ROLES = 'acl_roles';

    public function __construct(private CacheItemPoolInterface $permissionsCachePool, private PermissionService $permissionService)
    {
    }

    public function getResources(): array
    {
        $cacheItem = $this->permissionsCachePool->getItem(self::CACHE_ID_RESOURCES);

        if (!$cacheItem->isHit()) {
            $cacheItem->set($this->permissionService->getResources());
            $this->permissionsCachePool->saveDeferred($cacheItem);
        }

        return $cacheItem->get();
    }

    public function getRoles(): array
    {
        $cacheItem = $this->permissionsCachePool->getItem(self::CACHE_ID_ROLES);

        if (!$cacheItem->isHit()) {
            $cacheItem->set($this->permissionService->getRoles());
            $this->permissionsCachePool->saveDeferred($cacheItem);
        }

        return $cacheItem->get();
    }

    public function getPermissions(array $roleIds): array
    {
        return $this->permissionService->getPermissions($roleIds);
    }

    public function getPermissionsWithInheritance(array $roleIds): array
    {
        return $this->permissionService->getPermissionsWithInheritance($roleIds);
    }
}
