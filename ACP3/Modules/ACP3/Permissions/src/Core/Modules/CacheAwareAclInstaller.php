<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Core\Modules;

use ACP3\Core\Modules\AclInstaller;
use ACP3\Core\Modules\Installer\SchemaInterface;
use ACP3\Core\Modules\InstallerInterface;
use Psr\Cache\CacheItemPoolInterface;

class CacheAwareAclInstaller implements InstallerInterface
{
    /**
     * @var CacheItemPoolInterface
     */
    private $permissionsCachePool;
    /**
     * @var AclInstaller
     */
    private $aclInstaller;

    public function __construct(CacheItemPoolInterface $permissionsCachePool, AclInstaller $aclInstaller)
    {
        $this->permissionsCachePool = $permissionsCachePool;
        $this->aclInstaller = $aclInstaller;
    }

    public function install(SchemaInterface $schema)
    {
        $result = $this->aclInstaller->install($schema);

        $this->permissionsCachePool->clear();

        return $result;
    }

    public function uninstall(SchemaInterface $schema)
    {
        $result = $this->aclInstaller->uninstall($schema);

        $this->permissionsCachePool->clear();

        return $result;
    }
}
