<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Core\Modules;

use ACP3\Core\ACL\Model\Repository\PrivilegeRepositoryInterface;
use ACP3\Core\ACL\Model\Repository\RoleRepositoryInterface;
use ACP3\Core\Model\Repository\AbstractRepository;
use ACP3\Core\Modules\AclInstaller;
use ACP3\Core\Modules\Installer\SchemaInterface;
use ACP3\Core\Modules\SchemaHelper;
use Psr\Cache\CacheItemPoolInterface;

class CacheAwareAclInstaller extends AclInstaller
{
    /**
     * @var CacheItemPoolInterface
     */
    private $permissionsCachePool;

    public function __construct(CacheItemPoolInterface $permissionsCachePool, SchemaHelper $schemaHelper, RoleRepositoryInterface $roleRepository, AbstractRepository $ruleRepository, AbstractRepository $resourceRepository, PrivilegeRepositoryInterface $privilegeRepository)
    {
        parent::__construct($schemaHelper, $roleRepository, $ruleRepository, $resourceRepository, $privilegeRepository);

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
