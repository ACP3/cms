<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Services;

use ACP3\Core\ACL\PermissionServiceInterface;
use ACP3\Core\Cache;

class CachingPermissionService implements PermissionServiceInterface
{
    private const CACHE_ID_RESOURCES = 'acl_resources';
    private const CACHE_ID_ROLES = 'acl_roles';
    private const CACHE_ID_RULES = 'acl_rules_';

    /**
     * @var Cache
     */
    private $aclCache;
    /**
     * @var PermissionService
     */
    private $permissionService;

    public function __construct(Cache $aclCache, PermissionService $permissionService)
    {
        $this->aclCache = $aclCache;
        $this->permissionService = $permissionService;
    }

    public function getResources(): array
    {
        if (!$this->aclCache->contains(self::CACHE_ID_RESOURCES)) {
            $this->aclCache->save(self::CACHE_ID_RESOURCES, $this->permissionService->getResources());
        }

        return $this->aclCache->fetch(self::CACHE_ID_RESOURCES);
    }

    public function getRoles(): array
    {
        if (!$this->aclCache->contains(self::CACHE_ID_ROLES)) {
            $this->aclCache->save(self::CACHE_ID_ROLES, $this->permissionService->getRoles());
        }

        return $this->aclCache->fetch(self::CACHE_ID_ROLES);
    }

    public function getRules(array $roleIds): array
    {
        $cacheKey = self::CACHE_ID_RULES . implode(',', $roleIds);

        if (!$this->aclCache->contains($cacheKey)) {
            $this->aclCache->save($cacheKey, $this->permissionService->getRules($roleIds));
        }

        return $this->aclCache->fetch($cacheKey);
    }
}
