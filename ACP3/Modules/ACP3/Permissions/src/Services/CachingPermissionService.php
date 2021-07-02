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
    private const CACHE_ID_RULES = 'acl_rules_%s';

    /**
     * @var CacheItemPoolInterface
     */
    private $permissionsCachePool;
    /**
     * @var PermissionService
     */
    private $permissionService;

    public function __construct(CacheItemPoolInterface $permissionsCachePool, PermissionService $permissionService)
    {
        $this->permissionsCachePool = $permissionsCachePool;
        $this->permissionService = $permissionService;
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

    public function getRules(array $roleIds): array
    {
        $cacheKey = sprintf(self::CACHE_ID_RULES, implode(',', $roleIds));
        $cacheItem = $this->permissionsCachePool->getItem($cacheKey);

        if (!$cacheItem->isHit()) {
            $cacheItem->set($this->permissionService->getRules($roleIds));
            $this->permissionsCachePool->saveDeferred($cacheItem);
        }

        return $cacheItem->get();
    }
}
