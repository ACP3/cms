<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Core\Modules;

use ACP3\Core\ACL\Repository\AclPermissionRepositoryInterface;
use ACP3\Core\ACL\Repository\RoleRepositoryInterface;
use ACP3\Core\Installer\SchemaHelper;
use ACP3\Core\Installer\SchemaInterface;
use ACP3\Core\Repository\AbstractRepository;
use Psr\Cache\CacheItemPoolInterface;

class CacheAwareAclInstaller extends \ACP3\Core\Installer\AclInstaller
{
    /**
     * @var CacheItemPoolInterface
     */
    private $permissionsCachePool;

    public function __construct(CacheItemPoolInterface $permissionsCachePool, SchemaHelper $schemaHelper, RoleRepositoryInterface $roleRepository, AbstractRepository $resourceRepository, AclPermissionRepositoryInterface $permissionRepository)
    {
        parent::__construct($schemaHelper, $roleRepository, $resourceRepository, $permissionRepository);

        $this->permissionsCachePool = $permissionsCachePool;
    }

    public function install(SchemaInterface $schema, int $mode = self::INSTALL_RESOURCES_AND_RULES)
    {
        $result = parent::install($schema, $mode);

        $this->permissionsCachePool->clear();

        return $result;
    }

    public function uninstall(SchemaInterface $schema): bool
    {
        $result = parent::uninstall($schema);

        $this->permissionsCachePool->clear();

        return $result;
    }
}
