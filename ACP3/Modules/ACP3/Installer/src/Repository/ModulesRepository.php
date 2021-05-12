<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Repository;

use ACP3\Core\Model\Repository\ModuleAwareRepositoryInterface;

class ModulesRepository extends AbstractStubRepository implements ModuleAwareRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function getModuleId(string $moduleName): int
    {
        return 0;
    }

    /**
     * {@inheritDoc}
     */
    public function getModuleSchemaVersion(string $moduleName): int
    {
        return 0;
    }

    /**
     * {@inheritDoc}
     */
    public function moduleExists(string $moduleName): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function coreTablesExist(): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getInfoByModuleName(string $moduleName): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getInfoByModuleNameList(array $moduleNames): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getModuleNameById(int $moduleId): string
    {
        return '';
    }
}
